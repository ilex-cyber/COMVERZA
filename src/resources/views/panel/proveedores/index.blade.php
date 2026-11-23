@extends('layouts.panel')
@section('title', 'Proveedores')

@section('content')
<x-encabezado eyebrow="Red comercial" titulo="Proveedores" subtitulo="Proveedores con productos asignados no se pueden eliminar; desactívalos en su lugar.">
    @can('reportes.exportar')<a class="btn btn-outline" href="{{ route('panel.reportes.excel', 'proveedores') }}"><i data-lucide="sheet"></i> Excel</a>@endcan
    @can('proveedores.crear')<a class="btn btn-yellow" href="{{ route('panel.proveedores.create') }}"><i data-lucide="plus"></i> Nuevo proveedor</a>@endcan
</x-encabezado>

<div class="card">
    <form class="toolbar js-filtros" method="GET">
        <div class="search"><i data-lucide="search"></i><input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Razón social, RFC o contacto…"></div>
        <select name="ciudad" style="max-width:200px">
            <option value="">Todas las ciudades</option>
            @foreach ($ciudades as $c)<option @selected(request('ciudad') === $c)>{{ $c }}</option>@endforeach
        </select>
        <button class="btn btn-outline">Filtrar</button>
    </form>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Proveedor</th><th>Contacto</th><th>Ciudad</th><th>Calificación</th><th class="num">Productos</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse ($proveedores as $p)
                <tr>
                    <td><div class="cell-main">
                        <img src="{{ $p->logo_url ?? 'https://ui-avatars.com/api/?background=0a1c47&color=35a7ff&bold=true&name='.urlencode($p->razon_social) }}" alt="">
                        <div><strong>{{ $p->razon_social }}</strong><small>{{ $p->rfc }}</small></div>
                    </div></td>
                    <td>{{ $p->contacto }}<br><small style="color:var(--muted)">{{ $p->email }}</small></td>
                    <td>{{ $p->ciudad }}</td>
                    <td style="color:#f5b400;letter-spacing:2px">{{ str_repeat('★', $p->calificacion) }}<span style="color:#d5deec">{{ str_repeat('★', 5 - $p->calificacion) }}</span></td>
                    <td class="num"><b>{{ $p->productos_count }}</b></td>
                    <td><span class="badge {{ $p->activo ? 'badge-green' : 'badge-gray' }}">{{ $p->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td><div class="actions">
                        <a class="btn btn-outline btn-icon" href="{{ route('panel.proveedores.show', $p) }}" title="Ver"><i data-lucide="eye"></i></a>
                        @can('proveedores.editar')<a class="btn btn-outline btn-icon" href="{{ route('panel.proveedores.edit', $p) }}" title="Editar"><i data-lucide="pencil"></i></a>@endcan
                        @can('proveedores.eliminar')<x-eliminar :action="route('panel.proveedores.destroy', $p)" :nombre="$p->razon_social" />@endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i data-lucide="truck"></i>No hay proveedores registrados.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $proveedores->links() }}
</div>
@endsection
