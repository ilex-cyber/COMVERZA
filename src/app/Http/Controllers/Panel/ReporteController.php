<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mail\ComverzaMail;
use App\Models\Bitacora;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Support\Metricas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteController extends Controller
{
    /** Inventario / catálogo en PDF (DomPDF). */
    public function productosPdf()
    {
        $productos = Producto::with('categoria', 'proveedor')->orderBy('categoria_id')->orderBy('nombre')->get();
        Bitacora::registrar('exportar', 'reportes', 'Descargó el inventario en PDF');

        return Pdf::loadView('pdf.productos', compact('productos'))->setPaper('letter', 'landscape')
            ->stream('inventario-comverza-'.now()->format('Ymd').'.pdf');
    }

    /** Reporte ejecutivo de ventas en PDF. */
    public function ventasPdf()
    {
        Bitacora::registrar('exportar', 'reportes', 'Descargó el reporte de ventas en PDF');

        return $this->pdfVentas()->stream('ventas-comverza-'.now()->format('Ymd').'.pdf');
    }

    /** Exportación a Excel con PhpSpreadsheet. */
    public function excel(string $tipo)
    {
        [$titulo, $encabezados, $filas] = match ($tipo) {
            'productos' => ['Productos', ['SKU', 'Producto', 'Categoría', 'Proveedor', 'Costo', 'Precio', 'Margen %', 'Stock', 'Stock mínimo', 'Activo'],
                Producto::with('categoria', 'proveedor')->orderBy('nombre')->get()->map(fn ($p) => [
                    $p->sku, $p->nombre, $p->categoria?->nombre, $p->proveedor?->razon_social, (float) $p->costo, (float) $p->precio, $p->margen, $p->stock, $p->stock_minimo, $p->activo ? 'Sí' : 'No'])],
            'clientes' => ['Clientes', ['Tipo', 'Nombre', 'Empresa', 'RFC', 'Correo', 'Teléfono', 'Ciudad', 'Pedidos', 'Total comprado'],
                Cliente::withCount('pedidos')->withSum(['pedidos as comprado' => fn ($q) => $q->where('estado', '!=', 'cancelado')], 'total')->orderBy('nombre')->get()->map(fn ($c) => [
                    $c->tipo, $c->nombre, $c->empresa, $c->rfc, $c->email, $c->telefono, $c->ciudad, $c->pedidos_count, (float) $c->comprado])],
            'proveedores' => ['Proveedores', ['Razón social', 'RFC', 'Contacto', 'Correo', 'Teléfono', 'Ciudad', 'Calificación', 'Productos', 'Activo'],
                Proveedor::withCount('productos')->orderBy('razon_social')->get()->map(fn ($p) => [
                    $p->razon_social, $p->rfc, $p->contacto, $p->email, $p->telefono, $p->ciudad, $p->calificacion, $p->productos_count, $p->activo ? 'Sí' : 'No'])],
            'pedidos' => ['Pedidos', ['Folio', 'Fecha', 'Cliente', 'Vendedor', 'Estado', 'Método de pago', 'Subtotal', 'IVA', 'Total'],
                Pedido::with('cliente', 'usuario')->latest('fecha')->get()->map(fn ($p) => [
                    $p->folio, $p->fecha->format('d/m/Y'), $p->cliente->nombre_completo, $p->usuario?->name, $p->estado_texto,
                    Pedido::METODOS_PAGO[$p->metodo_pago], (float) $p->subtotal, (float) $p->iva, (float) $p->total])],
        };

        $libro = new Spreadsheet;
        $hoja = $libro->getActiveSheet()->setTitle($titulo);
        $hoja->setCellValue('A1', "COMVERZA · Reporte de {$titulo}");
        $hoja->setCellValue('A2', 'Generado el '.now()->format('d/m/Y H:i').' por '.auth()->user()->name);
        $hoja->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('06112F');
        $hoja->fromArray($encabezados, null, 'A4');
        $hoja->fromArray($filas->all(), null, 'A5');

        $ultimaCol = $hoja->getHighestColumn();
        $hoja->getStyle("A4:{$ultimaCol}4")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '06112F']],
        ]);
        $hoja->getStyle("A4:{$ultimaCol}4")->getBorders()->getBottom()->setBorderStyle('thick')->getColor()->setRGB('FFD533');
        foreach (range('A', $ultimaCol) as $col) {
            $hoja->getColumnDimension($col)->setAutoSize(true);
        }
        $hoja->setAutoFilter("A4:{$ultimaCol}".($filas->count() + 4));
        $hoja->freezePane('A5');

        Bitacora::registrar('exportar', 'reportes', "Exportó {$titulo} a Excel");

        return response()->streamDownload(function () use ($libro) {
            (new Xlsx($libro))->save('php://output');
        }, strtolower($titulo).'-comverza-'.now()->format('Ymd').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** Envía el reporte de ventas en PDF al correo indicado. */
    public function enviar(Request $request)
    {
        $datos = $request->validate([
            'destinatario' => ['required', 'email', 'max:150'],
            'mensaje' => ['nullable', 'string', 'max:500'],
        ]);
        $k = Metricas::kpis();

        Mail::to($datos['destinatario'])->send(new ComverzaMail(
            asunto: 'Reporte ejecutivo de ventas',
            titulo: 'Reporte ejecutivo de ventas',
            intro: ($datos['mensaje'] ?? '') ?: auth()->user()->name.' te compartió el reporte de ventas de COMVERZA. Encontrarás el detalle en el PDF adjunto.',
            filas: [
                ['Ventas del mes', '$'.number_format($k['ventas_mes'], 2)],
                ['Ventas últimos 12 meses', '$'.number_format($k['ventas_anual'], 2)],
                ['Ticket promedio', '$'.number_format($k['ticket'], 2)],
                ['Pedidos pendientes', (string) $k['pendientes']],
            ],
            replyToEmail: auth()->user()->email,
            adjuntos: [ComverzaMail::adjunto($this->pdfVentas()->output(), 'reporte-ventas-'.now()->format('Ymd').'.pdf')],
        ));
        Bitacora::registrar('enviar', 'reportes', "Envió el reporte de ventas a {$datos['destinatario']}");

        return back()->with('success', "Reporte enviado a {$datos['destinatario']}.");
    }

    private function pdfVentas()
    {
        return Pdf::loadView('pdf.ventas', [
            'kpis' => Metricas::kpis(),
            'mensual' => Metricas::ventasMensuales(),
            'categorias' => Metricas::ventasPorCategoria(),
            'topProductos' => Metricas::topProductos(10),
            'topClientes' => Metricas::topClientes(8),
            'vendedores' => Metricas::ventasPorVendedor(),
        ])->setPaper('letter');
    }
}
