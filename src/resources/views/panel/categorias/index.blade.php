@extends('layouts.panel')
@section('title', 'Categorías')

@section('content')
<x-encabezado eyebrow="Catálogo" titulo="Categorías" subtitulo="Organiza tus productos. Una categoría con productos asignados no se puede eliminar.">
    @can('categorias.crear')
        <a class="btn btn-yellow" href="{{ route('panel.categorias.create') }}"><i data-lucide="plus"></i> Nueva categoría</a>
    @endcan
</x-encabezado>

<div class="card">
    <form class="toolbar js-filtros" method="GET">
        <div class="search"><i data-lucide="search"></i><input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar categoría…" maxlength="80"></div>
        <select name="estado" style="max-width:180px">
            <option value="">Todas</option>
            <option value="1" @selected(request('estado') === '1')>Activas</option>
            <option value="0" @selected(request('estado') === '0')>Inactivas</option>
        </select>
        <button class="btn btn-outline">Filtrar</button>
    </form>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Categoría</th><th>Descripción</th><th class="num">Productos</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse ($categorias as $c)
                <tr>
                    <td><div class="cell-main">
                        @if ($c->imagen)<img src="{{ asset($c->imagen) }}" alt="">@else<span class="thumb" style="background:{{ $c->color }};display:grid;place-items:center;color:#fff;font-weight:800">{{ mb_substr($c->nombre, 0, 1) }}</span>@endif
                        <div><strong>{{ $c->nombre }}</strong><small><span class="dot" style="background:{{ $c->color }}"></span>{{ $c->color }}</small></div>
                    </div></td>
                    <td style="max-width:360px;color:var(--muted)">{{ $c->descripcion }}</td>
                    <td class="num"><b>{{ $c->productos_count }}</b></td>
                    <td><span class="badge {{ $c->activo ? 'badge-green' : 'badge-gray' }}">{{ $c->activo ? 'Activa' : 'Inactiva' }}</span></td>
                    <td><div class="actions">
                        @can('categorias.editar')<a class="btn btn-outline btn-icon" href="{{ route('panel.categorias.edit', $c) }}" title="Editar"><i data-lucide="pencil"></i></a>@endcan
                        @can('categorias.eliminar')
                            <x-eliminar :action="route('panel.categorias.destroy', $c)" :nombre="$c->nombre"
                                :mensaje="$c->productos_count ? 'Esta categoría tiene '.$c->productos_count.' producto(s); el sistema bloqueará la eliminación.' : 'Esta acción no se puede deshacer.'" />
                        @endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty"><i data-lucide="tags"></i>No hay categorías que coincidan.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $categorias->links() }}
</div>
@endsection
