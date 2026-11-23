@extends('layouts.panel')
@section('title', $producto->nombre)

@section('content')
<x-encabezado eyebrow="Producto" :titulo="$producto->nombre" :subtitulo="$producto->sku.' · '.$producto->categoria->nombre">
    <a class="btn btn-outline" href="{{ route('panel.productos.index') }}">← Volver</a>
    @can('productos.editar')<a class="btn btn-yellow" href="{{ route('panel.productos.edit', $producto) }}"><i data-lucide="pencil"></i> Editar</a>@endcan
</x-encabezado>

<div class="grid grid-3">
    <div class="card card-pad">
        <img src="{{ $producto->imagen_url }}" alt="" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:14px">
        @if ($producto->imagenes->count())
            <div class="previews">@foreach ($producto->imagenes as $img)<div class="preview"><img src="{{ asset($img->ruta) }}" alt=""></div>@endforeach</div>
        @endif
    </div>
    <div class="card card-pad">
        <dl class="detail-list">
            <dt>Precio</dt><dd>${{ number_format($producto->precio, 2) }}</dd>
            <dt>Costo</dt><dd>${{ number_format($producto->costo, 2) }} ({{ $producto->margen }}% margen)</dd>
            <dt>Stock</dt><dd><span class="badge {{ $producto->stock_bajo ? 'badge-red' : 'badge-green' }}">{{ $producto->stock }} {{ $producto->unidad }}(s)</span> · mín {{ $producto->stock_minimo }}</dd>
            <dt>Proveedor</dt><dd>{{ $producto->proveedor?->razon_social ?? '—' }}</dd>
            <dt>Vendidos</dt><dd>{{ $ventas->unidades }} unidades · ${{ number_format($ventas->importe, 2) }}</dd>
            <dt>Ficha técnica</dt><dd>@if ($producto->ficha_tecnica)<a class="link" href="{{ asset($producto->ficha_tecnica) }}" target="_blank">Ver PDF ↗</a>@else — @endif</dd>
            <dt>Estado</dt><dd><span class="badge {{ $producto->activo ? 'badge-green' : 'badge-gray' }}">{{ $producto->activo ? 'Activo' : 'Inactivo' }}</span> @if($producto->destacado)<span class="badge badge-yellow">Destacado</span>@endif</dd>
        </dl>
        <p style="margin-top:16px;color:var(--muted);line-height:1.6">{{ $producto->descripcion }}</p>
    </div>
    <div class="card card-pad" style="text-align:center">
        <h3 style="color:var(--navy);margin-bottom:6px">Código QR</h3>
        <p style="color:var(--muted);font-size:13px;margin-bottom:12px">Etiqueta para almacén: abre la ficha pública del producto.</p>
        <img src="{{ route('panel.productos.qr', $producto) }}" alt="QR" style="width:220px;margin:0 auto">
        <a class="btn btn-outline btn-sm" style="margin-top:14px" href="{{ route('panel.productos.qr', $producto) }}" download="qr-{{ $producto->sku }}.svg"><i data-lucide="download"></i> Descargar QR</a>
    </div>
</div>
@endsection
