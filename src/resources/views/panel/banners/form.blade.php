@extends('layouts.panel')
@section('title', $banner->exists ? 'Editar foto' : 'Nueva foto')

@section('content')
<x-encabezado eyebrow="Fotos de la landing" :titulo="$banner->exists ? 'Editar foto' : 'Agregar foto de presentación'" subtitulo="Usa fotos horizontales de al menos 1000 × 500 px. Se optimizan a WebP automáticamente.">
    <a class="btn btn-outline" href="{{ route('panel.banners.index') }}">← Volver</a>
</x-encabezado>

<form class="card card-pad js-validate" method="POST" enctype="multipart/form-data"
      action="{{ $banner->exists ? route('panel.banners.update', $banner) : route('panel.banners.store') }}">
    @csrf
    @if ($banner->exists) @method('PUT') @endif
    <div class="form-grid">
        <x-campo name="titulo" label="Título" :value="$banner->titulo" required minlength="3" maxlength="120" contador />
        <x-campo name="orden" label="Orden" type="number" :value="$banner->orden" required min="1" max="99" step="1" />
        <x-campo name="subtitulo" label="Subtítulo" :value="$banner->subtitulo" maxlength="255" contador full />
        <x-campo name="enlace" label="Enlace (opcional)" type="url" :value="$banner->enlace" maxlength="255" placeholder="https://" />
        <div class="field" style="align-self:end"><label class="switch"><input type="checkbox" name="activo" value="1" @checked(old('activo', $banner->activo))> Visible en la landing</label></div>
        <x-archivo name="imagen" label="Fotografía" :required="! $banner->exists" :actual="$banner->imagen" max-mb="4" min-width="1000" />
    </div>
    <div class="form-actions">
        <a class="btn btn-outline" href="{{ route('panel.banners.index') }}">Cancelar</a>
        <button class="btn btn-yellow" type="submit"><i data-lucide="save"></i> Guardar</button>
    </div>
</form>
@endsection
