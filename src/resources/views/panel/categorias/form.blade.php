@extends('layouts.panel')
@section('title', $categoria->exists ? 'Editar categoría' : 'Nueva categoría')

@section('content')
<x-encabezado eyebrow="Categorías" :titulo="$categoria->exists ? 'Editar «'.$categoria->nombre.'»' : 'Nueva categoría'" subtitulo="Los campos con * son obligatorios.">
    <a class="btn btn-outline" href="{{ route('panel.categorias.index') }}">← Volver</a>
</x-encabezado>

<form class="card card-pad js-validate" method="POST" enctype="multipart/form-data"
      action="{{ $categoria->exists ? route('panel.categorias.update', $categoria) : route('panel.categorias.store') }}">
    @csrf
    @if ($categoria->exists) @method('PUT') @endif
    <div class="form-grid">
        <x-campo name="nombre" label="Nombre" :value="$categoria->nombre" required minlength="3" maxlength="80" contador hint="Debe ser único. Ej. Tecnología, Papelería." />
        <div class="field">
            <label for="f_color">Color de identificación <span class="req">*</span></label>
            <input id="f_color" type="color" name="color" value="{{ old('color', $categoria->color) }}" required>
            @error('color')<div class="error">⚠ {{ $message }}</div>@enderror
        </div>
        <x-campo name="descripcion" label="Descripción" type="textarea" :value="$categoria->descripcion" maxlength="500" contador full />
        <x-archivo name="imagen" label="Imagen de la categoría" :actual="$categoria->imagen" min-width="200" hint="Se optimiza automáticamente a WebP (Intervention Image)." />
        <div class="field full">
            <label class="switch"><input type="checkbox" name="activo" value="1" @checked(old('activo', $categoria->activo))> Categoría activa</label>
            @error('activo')<div class="error">⚠ {{ $message }}</div>@enderror
        </div>
    </div>
    <div class="form-actions">
        <a class="btn btn-outline" href="{{ route('panel.categorias.index') }}">Cancelar</a>
        <button class="btn btn-yellow" type="submit"><i data-lucide="save"></i> Guardar</button>
    </div>
</form>
@endsection
