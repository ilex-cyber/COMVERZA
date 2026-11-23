<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Support\Metricas;

class DashboardController extends Controller
{
    /** Dashboard de impacto con datos reales de la base. */
    public function index()
    {
        return view('panel.dashboard', [
            'kpis' => Metricas::kpis(),
            'mensual' => Metricas::ventasMensuales(),
            'porCategoria' => Metricas::ventasPorCategoria(),
            'porEstado' => Metricas::pedidosPorEstado(),
            'topProductos' => Metricas::topProductos(),
            'topClientes' => Metricas::topClientes(),
            'vendedores' => Metricas::ventasPorVendedor(),
            'stockBajo' => Metricas::stockBajo(),
            'inventario' => Metricas::inventarioPorCategoria(),
            'recientes' => Pedido::with('cliente')->latest('fecha')->latest('id')->limit(6)->get(),
        ]);
    }

    /** Constructor manual de gráficas (el usuario captura etiquetas y valores). */
    public function graficas()
    {
        $mensual = Metricas::ventasMensuales();
        $cat = Metricas::ventasPorCategoria();

        return view('panel.graficas', [
            'presets' => [
                'ventas' => ['titulo' => 'Ventas mensuales (MXN)', 'tipo' => 'line', 'etiquetas' => $mensual['labels'], 'valores' => $mensual['ventas']],
                'categorias' => ['titulo' => 'Ventas por categoría (MXN)', 'tipo' => 'doughnut', 'etiquetas' => $cat['labels'], 'valores' => $cat['valores']],
                'estados' => ['titulo' => 'Pedidos por estado', 'tipo' => 'bar', 'etiquetas' => array_values(Pedido::ESTADOS), 'valores' => array_values(Metricas::pedidosPorEstado())],
            ],
        ]);
    }

    /** Consola para consumir la API con JavaScript (GET, POST, PUT, DELETE). */
    public function consolaApi()
    {
        return view('panel.consola-api');
    }
}
