@extends('layouts.panel')
@section('title', 'Dashboard')

@php
    $money = fn ($v) => '$'.number_format($v, 2);
    $trend = function ($v) {
        if ($v === null) return '<span class="trend">Sin comparación</span>';
        $c = $v >= 0 ? 'up' : 'down';
        return '<span class="trend '.$c.'">'.($v >= 0 ? '▲ ' : '▼ ').abs($v).'% vs. mes anterior</span>';
    };
@endphp

@section('content')
<x-encabezado eyebrow="Dashboard de impacto" :titulo="'Hola, '.explode(' ', auth()->user()->name)[0]" subtitulo="Así va tu negocio hoy. Datos en tiempo real de pedidos, inventario y clientes.">
    @can('reportes.exportar')
        <a class="btn btn-outline" href="{{ route('panel.reportes.ventas.pdf') }}" target="_blank"><i data-lucide="file-text"></i> Reporte PDF</a>
        <a class="btn btn-outline" href="{{ route('panel.reportes.excel', 'pedidos') }}"><i data-lucide="sheet"></i> Excel</a>
        <button class="btn btn-yellow" type="button" id="btnEnviarReporte"><i data-lucide="send"></i> Enviar por correo</button>
    @endcan
</x-encabezado>

<div class="grid grid-4" style="margin-bottom:20px">
    <div class="card kpi">
        <div class="kpi-icon"><i data-lucide="trending-up"></i></div>
        <div><small>Ventas del mes</small><strong>{{ $money($kpis['ventas_mes']) }}</strong>{!! $trend($kpis['ventas_var']) !!}</div>
    </div>
    <div class="card kpi">
        <div class="kpi-icon yellow"><i data-lucide="shopping-cart"></i></div>
        <div><small>Pedidos del mes</small><strong>{{ $kpis['pedidos_mes'] }}</strong>{!! $trend($kpis['pedidos_var']) !!}</div>
    </div>
    <div class="card kpi">
        <div class="kpi-icon purple"><i data-lucide="receipt"></i></div>
        <div><small>Ticket promedio (12 m)</small><strong>{{ $money($kpis['ticket']) }}</strong><span class="trend">{{ $money($kpis['ventas_anual']) }} acumulado</span></div>
    </div>
    <div class="card kpi">
        <div class="kpi-icon {{ $kpis['stock_bajo'] ? 'red' : 'green' }}"><i data-lucide="package-search"></i></div>
        <div><small>Productos con stock bajo</small><strong>{{ $kpis['stock_bajo'] }}</strong><span class="trend">de {{ $kpis['productos'] }} productos activos</span></div>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom:20px">
    <div class="card span-2">
        <div class="card-head"><div><h3>Ventas y pedidos · últimos 12 meses</h3><small>Ingresos (barras) y volumen de pedidos (línea)</small></div></div>
        <div class="chart-box lg"><canvas id="chVentas"></canvas></div>
    </div>
    <div class="card">
        <div class="card-head"><div><h3>Ventas por categoría</h3><small>Participación en ingresos</small></div></div>
        <div class="chart-box lg"><canvas id="chCategorias"></canvas></div>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom:20px">
    <div class="card">
        <div class="card-head"><div><h3>Pedidos por estado</h3><small>Tasa de cancelación: {{ $kpis['cancelacion'] }}%</small></div></div>
        <div class="chart-box"><canvas id="chEstados"></canvas></div>
    </div>
    <div class="card">
        <div class="card-head"><div><h3>Desempeño por vendedor</h3><small>Ventas de los últimos 12 meses</small></div></div>
        <div class="chart-box"><canvas id="chVendedores"></canvas></div>
    </div>
    <div class="card">
        <div class="card-head"><div><h3>Inventario por categoría</h3><small>Valor: {{ $money($kpis['valor_inventario']) }}</small></div></div>
        <div class="chart-box"><canvas id="chInventario"></canvas></div>
    </div>
</div>

<div class="grid grid-3">
    <div class="card">
        <div class="card-head"><h3>Productos más vendidos</h3></div>
        <ul class="mini-list">
            @php $maxP = $topProductos->max('importe') ?: 1; @endphp
            @foreach ($topProductos as $p)
                <li><div class="grow"><strong>{{ $p->nombre }}</strong><small>{{ $p->unidades }} unidades · {{ $money($p->importe) }}</small>
                    <div class="progress"><span style="width:{{ $p->importe / $maxP * 100 }}%"></span></div></div></li>
            @endforeach
        </ul>
    </div>
    <div class="card">
        <div class="card-head"><h3>Mejores clientes</h3></div>
        <ul class="mini-list">
            @foreach ($topClientes as $c)
                <li><img class="thumb" style="border-radius:50%" src="https://ui-avatars.com/api/?background=06112f&color=ffd533&bold=true&name={{ urlencode($c->empresa ?? $c->nombre) }}" alt="">
                    <div class="grow"><strong>{{ $c->empresa ?? $c->nombre }}</strong><small>{{ $c->pedidos_count }} pedidos</small></div>
                    <b>{{ $money($c->comprado) }}</b></li>
            @endforeach
        </ul>
    </div>
    <div class="card">
        <div class="card-head"><h3>Alertas de inventario</h3>@can('productos.ver')<a class="link" href="{{ route('panel.productos.index', ['stock' => 'bajo']) }}">Ver todo</a>@endcan</div>
        <ul class="mini-list">
            @forelse ($stockBajo as $p)
                <li><img class="thumb" src="{{ $p->imagen_url }}" alt=""><div class="grow"><strong>{{ $p->nombre }}</strong><small>{{ $p->categoria->nombre }}</small></div>
                    <span class="badge {{ $p->stock == 0 ? 'badge-red' : 'badge-yellow' }}">{{ $p->stock }} / mín {{ $p->stock_minimo }}</span></li>
            @empty
                <li><div class="grow"><strong>Todo en orden</strong><small>Ningún producto por debajo del mínimo.</small></div></li>
            @endforelse
        </ul>
    </div>
</div>

<div class="card" style="margin-top:20px">
    <div class="card-head"><h3>Pedidos recientes</h3>@can('pedidos.ver')<a class="link" href="{{ route('panel.pedidos.index') }}">Ver todos</a>@endcan</div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Folio</th><th>Cliente</th><th>Fecha</th><th>Estado</th><th class="num">Total</th></tr></thead>
            <tbody>
            @foreach ($recientes as $p)
                <tr>
                    <td><a class="link" href="{{ route('panel.pedidos.show', $p) }}">{{ $p->folio }}</a></td>
                    <td>{{ $p->cliente->nombre_completo }}</td>
                    <td>{{ $p->fecha->format('d/m/Y') }}</td>
                    <td><span class="badge badge-{{ $p->estado }}">{{ $p->estado_texto }}</span></td>
                    <td class="num"><b>{{ $money($p->total) }}</b></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<form id="formEnviarReporte" method="POST" action="{{ route('panel.reportes.enviar') }}" style="display:none">
    @csrf
    <input name="destinatario"><input name="mensaje">
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    Chart.defaults.font.family = 'Inter, Arial, sans-serif';
    Chart.defaults.color = '#7183a0';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    const mxn = v => '$' + Number(v).toLocaleString('es-MX', { maximumFractionDigits: 0 });
    const mensual = @json($mensual);

    new Chart(chVentas, {
        data: {
            labels: mensual.labels,
            datasets: [
                { type: 'bar', label: 'Ventas', data: mensual.ventas, backgroundColor: '#1677ff', borderRadius: 8, yAxisID: 'y', order: 2 },
                { type: 'line', label: 'Pedidos', data: mensual.pedidos, borderColor: '#ffbd1c', backgroundColor: '#ffd533', tension: .35, yAxisID: 'y1', order: 1, pointRadius: 4 },
            ],
        },
        options: {
            maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
            scales: {
                y: { ticks: { callback: mxn }, grid: { color: '#eef2f9' } },
                y1: { position: 'right', grid: { display: false }, beginAtZero: true },
                x: { grid: { display: false } },
            },
            plugins: { tooltip: { callbacks: { label: c => c.dataset.yAxisID === 'y' ? ` Ventas: ${mxn(c.raw)}` : ` Pedidos: ${c.raw}` } } },
        },
    });

    const cat = @json($porCategoria);
    new Chart(chCategorias, {
        type: 'doughnut',
        data: { labels: cat.labels, datasets: [{ data: cat.valores, backgroundColor: cat.colores, borderWidth: 2, borderColor: '#fff' }] },
        options: { maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: c => ` ${c.label}: ${mxn(c.raw)}` } } } },
    });

    const estados = @json($porEstado);
    new Chart(chEstados, {
        type: 'polarArea',
        data: { labels: ['Pendiente', 'En proceso', 'Enviado', 'Entregado', 'Cancelado'], datasets: [{ data: Object.values(estados), backgroundColor: ['#ffd533cc', '#1677ffcc', '#784cffcc', '#12b76acc', '#f04438cc'] }] },
        options: { maintainAspectRatio: false, plugins: { legend: { position: 'right' } }, scales: { r: { ticks: { display: false } } } },
    });

    const vend = @json($vendedores);
    new Chart(chVendedores, {
        type: 'bar',
        data: { labels: vend.map(v => v.name), datasets: [{ label: 'Ventas', data: vend.map(v => v.ventas), backgroundColor: ['#06112f', '#1677ff', '#784cff', '#35a7ff'], borderRadius: 8 }] },
        options: { indexAxis: 'y', maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + mxn(c.raw) } } }, scales: { x: { ticks: { callback: mxn } }, y: { grid: { display: false } } } },
    });

    const inv = @json($inventario);
    new Chart(chInventario, {
        type: 'radar',
        data: { labels: inv.map(i => i.nombre), datasets: [{ label: 'Unidades', data: inv.map(i => i.unidades || 0), backgroundColor: 'rgba(120,76,255,.2)', borderColor: '#784cff', pointBackgroundColor: '#784cff' }] },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { r: { ticks: { display: false }, pointLabels: { font: { size: 10 } } } } },
    });

    // Enviar reporte por correo (SweetAlert2 + validación)
    const btn = document.getElementById('btnEnviarReporte');
    btn && btn.addEventListener('click', async () => {
        const { value } = await Swal.fire({
            title: 'Enviar reporte de ventas',
            html: '<input id="sw-email" type="email" class="swal2-input" placeholder="correo@empresa.com" value="{{ auth()->user()->email }}">' +
                  '<textarea id="sw-msg" class="swal2-textarea" placeholder="Mensaje opcional" maxlength="500"></textarea>',
            confirmButtonText: 'Enviar PDF', confirmButtonColor: '#1677ff', showCancelButton: true, cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const email = document.getElementById('sw-email').value.trim();
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) { Swal.showValidationMessage('Escribe un correo válido'); return false; }
                return { email, msg: document.getElementById('sw-msg').value };
            },
        });
        if (!value) return;
        const f = document.getElementById('formEnviarReporte');
        f.destinatario.value = value.email; f.mensaje.value = value.msg;
        Swal.fire({ title: 'Generando y enviando…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        f.submit();
    });
});
</script>
@endpush
