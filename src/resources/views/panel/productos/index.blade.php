@extends('layouts.panel')
@section('title', 'Productos')

@section('content')
<x-encabezado eyebrow="Inventario" titulo="Productos" subtitulo="Productos vendidos en algún pedido no se pueden eliminar; desactívalos para ocultarlos.">
    @can('reportes.exportar')
        <a class="btn btn-outline" href="{{ route('panel.reportes.productos.pdf') }}" target="_blank"><i data-lucide="file-text"></i> PDF</a>
        <a class="btn btn-outline" href="{{ route('panel.reportes.excel', 'productos') }}"><i data-lucide="sheet"></i> Excel</a>
    @endcan
    @can('productos.crear')<a class="btn btn-yellow" href="{{ route('panel.productos.create') }}"><i data-lucide="plus"></i> Nuevo producto</a>@endcan
</x-encabezado>

<div class="card">
    <form class="toolbar js-filtros" method="GET">
        <div class="search"><i data-lucide="search"></i><input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o SKU…"></div>
        <select name="categoria" style="max-width:220px">
            <option value="">Todas las categorías</option>
            @foreach ($categorias as $id => $nombre)<option value="{{ $id }}" @selected(request('categoria') == $id)>{{ $nombre }}</option>@endforeach
        </select>
        <select name="stock" style="max-width:180px">
            <option value="">Todo el stock</option>
            <option value="bajo" @selected(request('stock') === 'bajo')>Stock bajo</option>
        </select>
        <button class="btn btn-outline">Filtrar</button>
    </form>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Producto</th><th>Categoría</th><th>Proveedor</th><th class="num">Precio</th><th class="num">Margen</th><th class="num">Stock</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse ($productos as $p)
                <tr>
                    <td><div class="cell-main"><img src="{{ $p->imagen_url }}" alt=""><div><strong>{{ $p->nombre }} @if($p->destacado)<span title="Destacado" style="color:#f5b400">★</span>@endif</strong><small>{{ $p->sku }}</small></div></div></td>
                    <td><span class="dot" style="background:{{ $p->categoria->color }}"></span>{{ $p->categoria->nombre }}</td>
                    <td style="color:var(--muted)">{{ $p->proveedor?->razon_social ?? '—' }}</td>
                    <td class="num"><b>${{ number_format($p->precio, 2) }}</b></td>
                    <td class="num">{{ $p->margen }}%</td>
                    <td class="num"><span class="badge {{ $p->stock == 0 ? 'badge-red' : ($p->stock_bajo ? 'badge-yellow' : 'badge-green') }}">{{ $p->stock }}</span></td>
                    <td><span class="badge {{ $p->activo ? 'badge-green' : 'badge-gray' }}">{{ $p->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td><div class="actions">
                        <a class="btn btn-outline btn-icon" href="{{ route('panel.productos.show', $p) }}" title="Ver"><i data-lucide="eye"></i></a>
                        @can('productos.editar')<a class="btn btn-outline btn-icon" href="{{ route('panel.productos.edit', $p) }}" title="Editar"><i data-lucide="pencil"></i></a>@endcan
                        @can('productos.eliminar')<x-eliminar :action="route('panel.productos.destroy', $p)" :nombre="$p->nombre" />@endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty"><i data-lucide="package"></i>No hay productos que coincidan.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $productos->links() }}
</div>
@endsection
