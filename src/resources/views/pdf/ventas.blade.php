<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Reporte de ventas</title>@include('pdf._estilos')</head>
<body>
<div class="footer">COMVERZA · Reporte ejecutivo de ventas · Generado el {{ now()->format('d/m/Y H:i') }}</div>
<div class="head"><table><tr>
    <td><div class="brand">COMVER<span>ZA</span></div><div class="muted">Business Platform</div></td>
    <td style="text-align:right"><div style="font-size:16px;font-weight:bold">REPORTE EJECUTIVO DE VENTAS</div><div class="muted">Últimos 12 meses · cifras en MXN con IVA</div></td>
</tr></table></div>

<table class="kpis" style="width:100%"><tr>
    <td><div class="kpi">Ventas del mes<b>${{ number_format($kpis['ventas_mes'], 2) }}</b></div></td>
    <td><div class="kpi" style="border-color:#ffd533">Ventas 12 meses<b>${{ number_format($kpis['ventas_anual'], 2) }}</b></div></td>
    <td><div class="kpi" style="border-color:#784cff">Ticket promedio<b>${{ number_format($kpis['ticket'], 2) }}</b></div></td>
    <td><div class="kpi" style="border-color:#12b76a">Clientes activos<b>{{ $kpis['clientes'] }}</b></div></td>
</tr></table>

<h2>Ventas mensuales</h2>
@php $max = max($mensual['ventas']) ?: 1; @endphp
<table class="t">
    @foreach ($mensual['labels'] as $i => $mes)
        <tr><td style="width:60px">{{ $mes }}</td>
            <td><div class="bar" style="width:{{ max(1, round($mensual['ventas'][$i] / $max * 100)) }}%"></div></td>
            <td class="num" style="width:110px">${{ number_format($mensual['ventas'][$i], 2) }}</td>
            <td class="num" style="width:70px">{{ $mensual['pedidos'][$i] }} ped.</td></tr>
    @endforeach
</table>

<table style="width:100%;margin-top:6px"><tr>
<td style="width:49%;vertical-align:top">
    <h2>Ventas por categoría</h2>
    @php $tc = collect($categorias['valores'])->sum() ?: 1; @endphp
    <table class="t">
        @foreach ($categorias['labels'] as $i => $c)
            <tr><td><span style="color:{{ $categorias['colores'][$i] }}">■</span> {{ $c }}</td><td class="num">${{ number_format($categorias['valores'][$i], 2) }}</td><td class="num">{{ round($categorias['valores'][$i] / $tc * 100, 1) }}%</td></tr>
        @endforeach
    </table>
</td>
<td style="width:2%"></td>
<td style="vertical-align:top">
    <h2>Desempeño por vendedor</h2>
    <table class="t">
        @foreach ($vendedores as $v)<tr><td>{{ $v->name }}</td><td class="num">{{ $v->pedidos }} ped.</td><td class="num">${{ number_format($v->ventas, 2) }}</td></tr>@endforeach
    </table>
</td>
</tr></table>

<h2>Top 10 productos</h2>
<table class="t">
    <thead><tr><th>#</th><th>SKU</th><th>Producto</th><th class="num">Unidades</th><th class="num">Importe</th></tr></thead>
    @foreach ($topProductos as $i => $p)<tr><td>{{ $i + 1 }}</td><td>{{ $p->sku }}</td><td>{{ $p->nombre }}</td><td class="num">{{ $p->unidades }}</td><td class="num">${{ number_format($p->importe, 2) }}</td></tr>@endforeach
</table>

<h2>Mejores clientes</h2>
<table class="t">
    <thead><tr><th>Cliente</th><th class="num">Pedidos</th><th class="num">Total comprado</th></tr></thead>
    @foreach ($topClientes as $c)<tr><td>{{ $c->empresa ?? $c->nombre }}</td><td class="num">{{ $c->pedidos_count }}</td><td class="num">${{ number_format($c->comprado, 2) }}</td></tr>@endforeach
</table>
</body></html>
