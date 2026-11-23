@extends('layouts.panel')
@section('title', 'Pedidos')

@section('content')
<x-encabezado eyebrow="Ventas" titulo="Pedidos" subtitulo="Solo se editan pedidos pendientes o en proceso. Los enviados o entregados no se pueden eliminar.">
    @can('reportes.exportar')<a class="btn btn-outline" href="{{ route('panel.reportes.excel', 'pedidos') }}"><i data-lucide="sheet"></i> Excel</a>@endcan
    @can('pedidos.crear')<a class="btn btn-yellow" href="{{ route('panel.pedidos.create') }}"><i data-lucide="plus"></i> Nuevo pedido</a>@endcan
</x-encabezado>

<div class="card">
    <form class="toolbar js-filtros" method="GET">
        <div class="search"><i data-lucide="search"></i><input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Folio o cliente…"></div>
        <select name="estado" style="max-width:170px">
            <option value="">Todos los estados</option>
            @foreach (\App\Models\Pedido::ESTADOS as $k => $v)<option value="{{ $k }}" @selected(request('estado') === $k)>{{ $v }}</option>@endforeach
        </select>
        <input type="date" name="desde" value="{{ request('desde') }}" style="max-width:160px" title="Desde">
        <input type="date" name="hasta" value="{{ request('hasta') }}" style="max-width:160px" title="Hasta">
        <button class="btn btn-outline">Filtrar</button>
    </form>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Folio</th><th>Cliente</th><th>Fecha</th><th>Vendedor</th><th>Estado</th><th class="num">Total</th><th></th></tr></thead>
            <tbody>
            @forelse ($pedidos as $p)
                <tr>
                    <td><a class="link" href="{{ route('panel.pedidos.show', $p) }}"><b>{{ $p->folio }}</b></a></td>
                    <td>{{ $p->cliente->nombre_completo }}</td>
                    <td>{{ $p->fecha->format('d/m/Y') }}</td>
                    <td style="color:var(--muted)">{{ $p->usuario?->name ?? '—' }}</td>
                    <td><span class="badge badge-{{ $p->estado }}">{{ $p->estado_texto }}</span></td>
                    <td class="num"><b>${{ number_format($p->total, 2) }}</b></td>
                    <td><div class="actions">
                        <a class="btn btn-outline btn-icon" href="{{ route('panel.pedidos.pdf', $p) }}" target="_blank" title="PDF"><i data-lucide="file-text"></i></a>
                        @can('pedidos.editar')@if ($p->editable)<a class="btn btn-outline btn-icon" href="{{ route('panel.pedidos.edit', $p) }}" title="Editar"><i data-lucide="pencil"></i></a>@endif @endcan
                        @can('pedidos.eliminar')@unless (in_array($p->estado, ['enviado', 'entregado']))<x-eliminar :action="route('panel.pedidos.destroy', $p)" :nombre="$p->folio" mensaje="El stock de sus productos se devolverá al inventario." />@endunless @endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i data-lucide="shopping-cart"></i>No hay pedidos que coincidan.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $pedidos->links() }}
</div>
@endsection
