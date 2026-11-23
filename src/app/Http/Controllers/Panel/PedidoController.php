<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mail\ComverzaMail;
use App\Models\Bitacora;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PedidoController extends Controller implements HasMiddleware
{
    use ConPermisos;

    public static function middleware(): array
    {
        return self::permisosDe('pedidos');
    }

    public function index(Request $request)
    {
        $pedidos = Pedido::with('cliente', 'usuario')
            ->when($request->buscar, fn ($q, $b) => $q->where(fn ($w) => $w->where('folio', 'like', "%{$b}%")
                ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$b}%")->orWhere('empresa', 'like', "%{$b}%"))))
            ->when($request->estado, fn ($q, $e) => $q->where('estado', $e))
            ->when($request->desde, fn ($q, $d) => $q->whereDate('fecha', '>=', $d))
            ->when($request->hasta, fn ($q, $h) => $q->whereDate('fecha', '<=', $h))
            ->latest('fecha')->latest('id')->paginate(12)->withQueryString();

        return view('panel.pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        return view('panel.pedidos.form', $this->catalogos() + ['pedido' => new Pedido([
            'folio' => Pedido::siguienteFolio(), 'fecha' => now(), 'estado' => 'pendiente', 'metodo_pago' => 'transferencia',
        ])]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $items = $this->validarItems($request);

        $pedido = DB::transaction(function () use ($datos, $items) {
            $pedido = Pedido::create($datos + ['folio' => Pedido::siguienteFolio(), 'user_id' => auth()->id()]);
            $this->guardarItems($pedido, $items);

            return $pedido;
        });
        Bitacora::registrar('crear', 'pedidos', "Creó el pedido {$pedido->folio} por $".number_format($pedido->total, 2));

        // Confirmación por correo con el PDF adjunto
        $this->enviarConfirmacion($pedido);
        $this->alertarStockBajo($items);

        return redirect()->route('panel.pedidos.show', $pedido)->with('success', "Pedido {$pedido->folio} registrado y enviado por correo al cliente.");
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'usuario', 'detalles.producto');

        return view('panel.pedidos.show', compact('pedido'));
    }

    public function edit(Pedido $pedido)
    {
        if (! $pedido->editable) {
            return redirect()->route('panel.pedidos.show', $pedido)->with('error', "El pedido {$pedido->folio} está {$pedido->estado_texto} y ya no se puede editar.");
        }
        $pedido->load('detalles');

        return view('panel.pedidos.form', $this->catalogos() + compact('pedido'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        if (! $pedido->editable) {
            return redirect()->route('panel.pedidos.show', $pedido)->with('error', 'Este pedido ya no se puede editar.');
        }
        $datos = $this->validar($request, $pedido);
        $items = $this->validarItems($request, $pedido);

        DB::transaction(function () use ($pedido, $datos, $items) {
            $this->devolverStock($pedido);
            $pedido->detalles()->delete();
            $pedido->update($datos);
            if ($datos['estado'] !== 'cancelado') {
                $this->guardarItems($pedido, $items);
            } else {
                $pedido->recalcularTotales();
            }
        });
        Bitacora::registrar('editar', 'pedidos', "Editó el pedido {$pedido->folio} (estado: {$pedido->estado})");

        return redirect()->route('panel.pedidos.show', $pedido)->with('success', 'Pedido actualizado.');
    }

    public function destroy(Pedido $pedido)
    {
        if (in_array($pedido->estado, ['enviado', 'entregado'])) {
            return back()->with('error', "No se puede eliminar el pedido {$pedido->folio}: ya fue {$pedido->estado_texto}. Solo se eliminan pedidos pendientes, en proceso o cancelados.");
        }

        DB::transaction(function () use ($pedido) {
            if ($pedido->estado !== 'cancelado') {
                $this->devolverStock($pedido);
            }
            $pedido->delete();
        });
        Bitacora::registrar('eliminar', 'pedidos', "Eliminó el pedido {$pedido->folio}");

        return redirect()->route('panel.pedidos.index')->with('success', "Pedido {$pedido->folio} eliminado y stock devuelto.");
    }

    /** PDF del pedido / nota de venta (DomPDF). */
    public function pdf(Pedido $pedido)
    {
        return $this->generarPdf($pedido)->stream("pedido-{$pedido->folio}.pdf");
    }

    public function enviar(Pedido $pedido)
    {
        $this->enviarConfirmacion($pedido);

        return back()->with('success', "Se envió el pedido {$pedido->folio} a {$pedido->cliente->email}.");
    }

    // ------------------------------------------------------------------

    private function validar(Request $request, ?Pedido $p = null): array
    {
        return $request->validate([
            'cliente_id' => ['required', Rule::exists('clientes', 'id')->where('activo', true)],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'fecha_entrega' => ['nullable', 'date', 'after_or_equal:fecha'],
            'estado' => ['required', Rule::in(array_keys(Pedido::ESTADOS))],
            'metodo_pago' => ['required', Rule::in(array_keys(Pedido::METODOS_PAGO))],
            'notas' => ['nullable', 'string', 'max:1000'],
        ], [
            'cliente_id.exists' => 'Selecciona un cliente activo.',
            'fecha.before_or_equal' => 'La fecha del pedido no puede ser futura.',
            'fecha_entrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a la del pedido.',
        ]);
    }

    /** Valida renglones: productos existentes, sin duplicados y con stock suficiente. */
    private function validarItems(Request $request, ?Pedido $pedido = null): array
    {
        $request->merge(['items' => collect($request->input('items', []))->filter(fn ($i) => ! empty($i['producto_id']))->values()->all()]);

        $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.producto_id' => ['required', 'distinct', Rule::exists('productos', 'id')->where('activo', true)],
            'items.*.cantidad' => ['required', 'integer', 'min:1', 'max:10000'],
        ], [
            'items.required' => 'Agrega al menos un producto al pedido.',
            'items.*.producto_id.distinct' => 'Un producto está repetido; ajusta la cantidad en un solo renglón.',
        ]);

        $previos = $pedido ? $pedido->detalles->pluck('cantidad', 'producto_id') : collect();
        $errores = [];
        $items = [];
        foreach ($request->items as $i => $item) {
            $producto = Producto::find($item['producto_id']);
            $disponible = $producto->stock + (int) ($previos[$producto->id] ?? 0);
            if ($item['cantidad'] > $disponible && $request->estado !== 'cancelado') {
                $errores["items.{$i}.cantidad"] = "Stock insuficiente de «{$producto->nombre}»: solicitaste {$item['cantidad']} y hay {$disponible} disponible(s).";
            }
            $items[] = ['producto' => $producto, 'cantidad' => (int) $item['cantidad']];
        }
        if ($errores) {
            throw ValidationException::withMessages($errores);
        }

        return $items;
    }

    private function guardarItems(Pedido $pedido, array $items): void
    {
        foreach ($items as ['producto' => $producto, 'cantidad' => $cantidad]) {
            $producto->refresh();
            $pedido->detalles()->create([
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $producto->precio,
                'importe' => $producto->precio * $cantidad,
            ]);
            $producto->decrement('stock', $cantidad);
        }
        $pedido->recalcularTotales();
    }

    private function devolverStock(Pedido $pedido): void
    {
        foreach ($pedido->detalles as $d) {
            Producto::whereKey($d->producto_id)->increment('stock', $d->cantidad);
        }
    }

    private function generarPdf(Pedido $pedido)
    {
        $pedido->load('cliente', 'usuario', 'detalles.producto');

        return Pdf::loadView('pdf.pedido', compact('pedido'))->setPaper('letter');
    }

    private function enviarConfirmacion(Pedido $pedido): void
    {
        $pedido->load('cliente');
        Mail::to($pedido->cliente->email)->send(new ComverzaMail(
            asunto: "Pedido {$pedido->folio}",
            titulo: "Recibimos tu pedido {$pedido->folio}",
            intro: "Hola {$pedido->cliente->nombre}, gracias por tu compra. Adjuntamos el detalle de tu pedido en PDF.",
            filas: [
                ['Folio', $pedido->folio],
                ['Fecha', $pedido->fecha->format('d/m/Y')],
                ['Estado', $pedido->estado_texto],
                ['Total', '$'.number_format($pedido->total, 2).' MXN'],
            ],
            adjuntos: [ComverzaMail::adjunto($this->generarPdf($pedido)->output(), "pedido-{$pedido->folio}.pdf")],
        ));
    }

    private function alertarStockBajo(array $items): void
    {
        $bajos = collect($items)->map(fn ($i) => $i['producto']->fresh())->filter->stock_bajo;
        if ($bajos->isEmpty()) {
            return;
        }
        Mail::to(config('app.admin_email'))->send(new ComverzaMail(
            asunto: 'Alerta de stock bajo',
            titulo: 'Productos por debajo del stock mínimo',
            intro: 'Después del último pedido, estos productos requieren reabastecimiento:',
            filas: $bajos->map(fn ($p) => ["{$p->sku} · {$p->nombre}", "{$p->stock} disponibles (mínimo {$p->stock_minimo})"])->values()->all(),
            botonTexto: 'Ver inventario',
            botonUrl: route('panel.productos.index', ['stock' => 'bajo']),
        ));
    }

    private function catalogos(): array
    {
        return [
            'clientes' => Cliente::where('activo', true)->orderBy('nombre')->get()->mapWithKeys(fn ($c) => [$c->id => $c->nombre_completo]),
            'productos' => Producto::where('activo', true)->orderBy('nombre')->get(['id', 'sku', 'nombre', 'precio', 'stock']),
        ];
    }
}
