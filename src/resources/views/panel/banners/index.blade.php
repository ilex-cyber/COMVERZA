@extends('layouts.panel')
@section('title', 'Fotos de la landing')

@section('content')
<x-encabezado eyebrow="Sitio web" titulo="Fotos de presentación" subtitulo="Imágenes del carrusel principal de la página de inicio. Debe existir al menos una activa.">
    <a class="btn btn-outline" href="{{ route('home') }}" target="_blank"><i data-lucide="external-link"></i> Ver landing</a>
    @can('banners.crear')<a class="btn btn-yellow" href="{{ route('panel.banners.create') }}"><i data-lucide="image-plus"></i> Agregar foto</a>@endcan
</x-encabezado>

<div class="grid grid-3">
    @forelse ($banners as $b)
        <div class="card" style="overflow:hidden">
            <div style="position:relative">
                <img src="{{ $b->imagen_url }}" alt="" style="width:100%;height:190px;object-fit:cover">
                <span class="badge {{ $b->activo ? 'badge-green' : 'badge-gray' }}" style="position:absolute;top:12px;left:12px">{{ $b->activo ? 'Visible' : 'Oculta' }}</span>
                <span class="badge badge-blue" style="position:absolute;top:12px;right:12px">#{{ $b->orden }}</span>
            </div>
            <div class="card-pad">
                <strong style="color:var(--navy)">{{ $b->titulo }}</strong>
                <p style="color:var(--muted);margin:6px 0 14px;font-size:13px">{{ $b->subtitulo }}</p>
                <div class="actions" style="justify-content:flex-start">
                    @can('banners.editar')<a class="btn btn-outline btn-sm" href="{{ route('panel.banners.edit', $b) }}"><i data-lucide="pencil"></i> Editar</a>@endcan
                    @can('banners.eliminar')<x-eliminar :action="route('panel.banners.destroy', $b)" :nombre="$b->titulo" texto="Eliminar" />@endcan
                </div>
            </div>
        </div>
    @empty
        <div class="card empty span-2">Aún no hay fotos.</div>
    @endforelse
</div>
@endsection
