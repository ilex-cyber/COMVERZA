@extends('layouts.panel')
@section('title', $permiso->exists ? 'Editar permiso' : 'Nuevo permiso')

@section('content')
<x-encabezado eyebrow="Permisos" :titulo="$permiso->exists ? 'Editar permiso' : 'Nuevo permiso'" subtitulo="Después de crearlo, asígnalo a uno o más roles.">
    <a class="btn btn-outline" href="{{ route('panel.permisos.index') }}">← Volver</a>
</x-encabezado>

<form class="card card-pad js-validate" style="max-width:640px" method="POST" action="{{ $permiso->exists ? route('panel.permisos.update', $permiso) : route('panel.permisos.store') }}">
    @csrf
    @if ($permiso->exists) @method('PUT') @endif
    <x-campo name="name" label="Nombre del permiso" :value="$permiso->name" required maxlength="60" pattern="[a-z_]+\.[a-z_]+"
             patron="Usa el formato modulo.accion en minúsculas (ej. inventario.ajustar)." placeholder="inventario.ajustar" hint="Formato modulo.accion, solo minúsculas y guion bajo." />
    <div class="form-actions">
        <a class="btn btn-outline" href="{{ route('panel.permisos.index') }}">Cancelar</a>
        <button class="btn btn-yellow" type="submit"><i data-lucide="save"></i> Guardar</button>
    </div>
</form>
@endsection
