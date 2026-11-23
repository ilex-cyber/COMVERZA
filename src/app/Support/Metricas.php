<?php

namespace App\Support;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Indicadores de negocio usados por el dashboard, el reporte PDF de ventas
 * y la página pública. Excluyen pedidos cancelados.
 */
class Metricas
{
    public static function ventasEntre(Carbon $desde, Carbon $hasta): float
    {
        return (float) Pedido::where('estado', '!=', 'cancelado')->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])->sum('total');
    }

    public static function kpis(): array
    {
        $inicioMes = now()->startOfMonth();
        $mesAnt = now()->subMonthNoOverflow();
        $ventasMes = self::ventasEntre($inicioMes, now());
        // Comparación justa: mismo número de días del mes anterior
        $ventasAnt = self::ventasEntre($mesAnt->copy()->startOfMonth(), $mesAnt->copy()->startOfMonth()->addDays(now()->day - 1));
        $pedidosMes = Pedido::where('fecha', '>=', $inicioMes)->where('estado', '!=', 'cancelado')->count();
        $pedidosAnt = Pedido::whereBetween('fecha', [$mesAnt->copy()->startOfMonth(), $mesAnt->copy()->startOfMonth()->addDays(now()->day - 1)])->where('estado', '!=', 'cancelado')->count();
        $anual = self::ventasEntre(now()->subMonths(11)->startOfMonth(), now());
        $pedidosAnual = Pedido::where('fecha', '>=', now()->subMonths(11)->startOfMonth())->where('estado', '!=', 'cancelado')->count();

        $var = fn ($a, $b) => $b > 0 ? round((($a - $b) / $b) * 100, 1) : null;

        return [
            'ventas_mes' => $ventasMes,
            'ventas_var' => $var($ventasMes, $ventasAnt),
            'pedidos_mes' => $pedidosMes,
            'pedidos_var' => $var($pedidosMes, $pedidosAnt),
            'ventas_anual' => $anual,
            'ticket' => $pedidosAnual ? $anual / $pedidosAnual : 0,
            'clientes' => Cliente::where('activo', true)->count(),
            'productos' => Producto::where('activo', true)->count(),
            'stock_bajo' => Producto::where('activo', true)->whereColumn('stock', '<=', 'stock_minimo')->count(),
            'pendientes' => Pedido::whereIn('estado', ['pendiente', 'en_proceso'])->count(),
            'valor_inventario' => (float) Producto::where('activo', true)->sum(DB::raw('stock * costo')),
            'cancelacion' => ($t = Pedido::where('fecha', '>=', now()->subMonths(11)->startOfMonth())->count())
                ? round(Pedido::where('fecha', '>=', now()->subMonths(11)->startOfMonth())->where('estado', 'cancelado')->count() / $t * 100, 1) : 0,
        ];
    }

    /** Ventas y número de pedidos de los últimos 12 meses. */
    public static function ventasMensuales(): array
    {
        $filas = Pedido::where('estado', '!=', 'cancelado')
            ->where('fecha', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') mes, SUM(total) ventas, COUNT(*) pedidos")
            ->groupBy('mes')->pluck('ventas', 'mes');
        $conteo = Pedido::where('estado', '!=', 'cancelado')
            ->where('fecha', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') mes, COUNT(*) pedidos")->groupBy('mes')->pluck('pedidos', 'mes');

        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $out = ['labels' => [], 'ventas' => [], 'pedidos' => []];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->startOfMonth()->subMonths($i);
            $out['labels'][] = $meses[$m->month - 1].' '.$m->format('y');
            $out['ventas'][] = round((float) ($filas[$m->format('Y-m')] ?? 0), 2);
            $out['pedidos'][] = (int) ($conteo[$m->format('Y-m')] ?? 0);
        }

        return $out;
    }

    public static function ventasPorCategoria(): array
    {
        $filas = DB::table('pedido_detalles as d')
            ->join('pedidos as p', 'p.id', '=', 'd.pedido_id')
            ->join('productos as pr', 'pr.id', '=', 'd.producto_id')
            ->join('categorias as c', 'c.id', '=', 'pr.categoria_id')
            ->where('p.estado', '!=', 'cancelado')
            ->groupBy('c.id', 'c.nombre', 'c.color')
            ->orderByDesc('importe')
            ->selectRaw('c.nombre, c.color, SUM(d.importe) importe')->get();

        return ['labels' => $filas->pluck('nombre'), 'valores' => $filas->pluck('importe')->map(fn ($v) => round($v, 2)), 'colores' => $filas->pluck('color')];
    }

    public static function topProductos(int $n = 5)
    {
        return DB::table('pedido_detalles as d')
            ->join('pedidos as p', 'p.id', '=', 'd.pedido_id')
            ->join('productos as pr', 'pr.id', '=', 'd.producto_id')
            ->where('p.estado', '!=', 'cancelado')
            ->groupBy('pr.id', 'pr.nombre', 'pr.sku')
            ->orderByDesc('importe')->limit($n)
            ->selectRaw('pr.id, pr.nombre, pr.sku, SUM(d.cantidad) unidades, SUM(d.importe) importe')->get();
    }

    public static function topClientes(int $n = 5)
    {
        return Cliente::withSum(['pedidos as comprado' => fn ($q) => $q->where('estado', '!=', 'cancelado')], 'total')
            ->withCount('pedidos')->orderByDesc('comprado')->limit($n)->get();
    }

    public static function pedidosPorEstado(): array
    {
        $filas = Pedido::selectRaw('estado, COUNT(*) total')->groupBy('estado')->pluck('total', 'estado');

        return collect(Pedido::ESTADOS)->map(fn ($txt, $k) => (int) ($filas[$k] ?? 0))->all();
    }

    public static function ventasPorVendedor()
    {
        return DB::table('pedidos as p')->join('users as u', 'u.id', '=', 'p.user_id')
            ->where('p.estado', '!=', 'cancelado')->where('p.fecha', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('u.id', 'u.name')->orderByDesc('ventas')
            ->selectRaw('u.name, SUM(p.total) ventas, COUNT(*) pedidos')->get();
    }

    public static function stockBajo(int $n = 6)
    {
        return Producto::with('categoria')->where('activo', true)->whereColumn('stock', '<=', 'stock_minimo')->orderBy('stock')->limit($n)->get();
    }

    public static function inventarioPorCategoria()
    {
        return Categoria::withSum('productos as unidades', 'stock')->orderBy('nombre')->get(['id', 'nombre', 'color']);
    }
}
