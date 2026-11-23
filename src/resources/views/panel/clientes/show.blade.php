@extends('layouts.panel')
@section('title', $cliente->nombre)

@section('content')
<x-encabezado eyebrow="Cliente" :titulo="$cliente->empresa ?? $cliente->nombre" :subtitulo="$cliente->tipo.' · '.$cliente->email">
    <a class="btn btn-outline" href="{{ route('panel.clientes.index') }}">← Volver</a>
    @can('pedidos.crear')<a class="btn btn-blue" href="{{ route('panel.pedidos.create', ['cliente' => $cliente->id]) }}"><i data-lucide="plus"></i> Nuevo pedido</a>@endcan
    @can('clientes.editar')<a class="btn btn-yellow" href="{{ route('panel.clientes.edit', $cliente) }}"><i data-lucide="pencil"></i> Editar</a>@endcan
</x-encabezado>

<div class="grid grid-3" style="margin-bottom:20px">
    <div class="card kpi"><div class="kpi-icon"><i data-lucide="wallet"></i></div><div><small>Total comprado</small><strong>${{ number_format($resumen['total'], 2) }}</strong></div></div>
    <div class="card kpi"><div class="kpi-icon yellow"><i data-lucide="shopping-bag"></i></div><div><small>Pedidos</small><strong>{{ $resumen['pedidos'] }}</strong></div></div>
    <div class="card kpi"><div class="kpi-icon purple"><i data-lucide="calendar"></i></div><div><small>Último pedido</small><strong>{{ $resumen['ultimo'] ? \Illuminate\Support\Carbon::parse($resumen['ultimo'])->format('d/m/Y') : '—' }}</strong></div></div>
</div>

<div class="grid grid-3">
    <div class="card card-pad">
        <dl class="detail-list">
            <dt>Contacto</dt><dd>{{ $cliente->nombre }}</dd>
            <dt>RFC</dt><dd>{{ $cliente->rfc ?: '—' }}</dd>
            <dt>Teléfono</dt><dd>{{ $cliente->telefono }}</dd>
            <dt>Dirección</dt><dd>{{ $cliente->direccion }} {{ $cliente->codigo_postal ? 'CP '.$cliente->codigo_postal : '' }}</dd>
            <dt>Ciudad</dt><dd>{{ $cliente->ciudad }}</dd>
            <dt>Crédito</dt><dd>${{ number_format($cliente->limite_credito, 2) }}</dd>
            <dt>Estado</dt><dd><span class="badge {{ $cliente->activo ? 'badge-green' : 'badge-gray' }}">{{ $cliente->activo ? 'Activo' : 'Inactivo' }}</span></dd>
        </dl>
    </div>
    <div class="card span-2">
        <div class="card-head"><h3>Historial de pedidos</h3></div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Folio</th><th>Fecha</th><th>Estado</th><th class="num">Total</th></tr></thead>
                <tbody>
                @forelse ($pedidos as $p)
                    <tr><td><a class="link" href="{{ route('panel.pedidos.show', $p) }}">{{ $p->folio }}</a></td><td>{{ $p->fecha->format('d/m/Y') }}</td>
                        <td><span class="badge badge-{{ $p->estado }}">{{ $p->estado_texto }}</span></td><td class="num"><b>${{ number_format($p->total, 2) }}</b></td></tr>
                @empty
                    <tr><td colspan="4"><div class="empty">Sin pedidos todavía.</div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $pedidos->links() }}
    </div>
</div>
@endsection
