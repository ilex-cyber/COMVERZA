@extends('layouts.panel')
@section('title', $proveedor->razon_social)

@section('content')
<x-encabezado eyebrow="Proveedor" :titulo="$proveedor->razon_social" :subtitulo="$proveedor->rfc.' · '.$proveedor->ciudad">
    <a class="btn btn-outline" href="{{ route('panel.proveedores.index') }}">← Volver</a>
    @can('proveedores.editar')<a class="btn btn-yellow" href="{{ route('panel.proveedores.edit', $proveedor) }}"><i data-lucide="pencil"></i> Editar</a>@endcan
</x-encabezado>

<div class="grid grid-3">
    <div class="card card-pad">
        <img src="{{ $proveedor->logo_url ?? 'https://ui-avatars.com/api/?size=200&background=0a1c47&color=35a7ff&bold=true&name='.urlencode($proveedor->razon_social) }}" style="width:96px;height:96px;border-radius:18px;object-fit:cover;margin-bottom:16px" alt="">
        <dl class="detail-list">
            <dt>Contacto</dt><dd>{{ $proveedor->contacto }}</dd>
            <dt>Correo</dt><dd><a class="link" href="mailto:{{ $proveedor->email }}">{{ $proveedor->email }}</a></dd>
            <dt>Teléfono</dt><dd>{{ $proveedor->telefono }}</dd>
            <dt>Dirección</dt><dd>{{ $proveedor->direccion ?: '—' }}</dd>
            <dt>Sitio web</dt><dd>{{ $proveedor->sitio_web ?: '—' }}</dd>
            <dt>Calificación</dt><dd style="color:#f5b400">{{ str_repeat('★', $proveedor->calificacion) }}</dd>
            <dt>Estado</dt><dd><span class="badge {{ $proveedor->activo ? 'badge-green' : 'badge-gray' }}">{{ $proveedor->activo ? 'Activo' : 'Inactivo' }}</span></dd>
            <dt>Constancia</dt><dd>@if ($proveedor->constancia_fiscal)<a class="link" target="_blank" href="{{ asset($proveedor->constancia_fiscal) }}">Ver PDF ↗</a>@else — @endif</dd>
        </dl>
    </div>
    <div class="card span-2">
        <div class="card-head"><h3>Productos que surte ({{ $proveedor->productos->count() }})</h3></div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Producto</th><th>Categoría</th><th class="num">Precio</th><th class="num">Stock</th></tr></thead>
                <tbody>
                @forelse ($proveedor->productos as $p)
                    <tr><td><div class="cell-main"><img src="{{ $p->imagen_url }}" alt=""><div><strong>{{ $p->nombre }}</strong><small>{{ $p->sku }}</small></div></div></td>
                        <td>{{ $p->categoria->nombre }}</td><td class="num">${{ number_format($p->precio, 2) }}</td>
                        <td class="num"><span class="badge {{ $p->stock_bajo ? 'badge-red' : 'badge-green' }}">{{ $p->stock }}</span></td></tr>
                @empty
                    <tr><td colspan="4"><div class="empty">Este proveedor aún no tiene productos.</div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
