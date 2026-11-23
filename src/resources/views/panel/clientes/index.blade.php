@extends('layouts.panel')
@section('title', 'Clientes')

@section('content')
<x-encabezado eyebrow="Cartera" titulo="Clientes" subtitulo="Clientes con historial de pedidos no se pueden eliminar.">
    @can('reportes.exportar')<a class="btn btn-outline" href="{{ route('panel.reportes.excel', 'clientes') }}"><i data-lucide="sheet"></i> Excel</a>@endcan
    @can('clientes.crear')<a class="btn btn-yellow" href="{{ route('panel.clientes.create') }}"><i data-lucide="plus"></i> Nuevo cliente</a>@endcan
</x-encabezado>

<div class="card">
    <form class="toolbar js-filtros" method="GET">
        <div class="search"><i data-lucide="search"></i><input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre, empresa, correo o RFC…"></div>
        <select name="tipo" style="max-width:180px">
            <option value="">Todos los tipos</option>
            <option @selected(request('tipo') === 'Empresa')>Empresa</option>
            <option @selected(request('tipo') === 'Persona')>Persona</option>
        </select>
        <button class="btn btn-outline">Filtrar</button>
    </form>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Cliente</th><th>Contacto</th><th>Ciudad</th><th class="num">Pedidos</th><th class="num">Comprado</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse ($clientes as $c)
                <tr>
                    <td><div class="cell-main">
                        <img src="https://ui-avatars.com/api/?background=06112f&color=ffd533&bold=true&name={{ urlencode($c->empresa ?? $c->nombre) }}" alt="">
                        <div><strong>{{ $c->empresa ?? $c->nombre }}</strong><small>{{ $c->tipo }}{{ $c->rfc ? ' · '.$c->rfc : '' }}</small></div>
                    </div></td>
                    <td>{{ $c->nombre }}<br><small style="color:var(--muted)">{{ $c->email }}</small></td>
                    <td>{{ $c->ciudad }}</td>
                    <td class="num">{{ $c->pedidos_count }}</td>
                    <td class="num"><b>${{ number_format($c->total_comprado ?? 0, 2) }}</b></td>
                    <td><span class="badge {{ $c->activo ? 'badge-green' : 'badge-gray' }}">{{ $c->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td><div class="actions">
                        <a class="btn btn-outline btn-icon" href="{{ route('panel.clientes.show', $c) }}" title="Ver"><i data-lucide="eye"></i></a>
                        @can('clientes.editar')<a class="btn btn-outline btn-icon" href="{{ route('panel.clientes.edit', $c) }}" title="Editar"><i data-lucide="pencil"></i></a>@endcan
                        @can('clientes.eliminar')<x-eliminar :action="route('panel.clientes.destroy', $c)" :nombre="$c->nombre" />@endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i data-lucide="building-2"></i>No hay clientes que coincidan.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $clientes->links() }}
</div>
@endsection
