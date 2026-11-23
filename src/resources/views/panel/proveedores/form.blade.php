@extends('layouts.panel')
@section('title', $proveedor->exists ? 'Editar proveedor' : 'Nuevo proveedor')

@section('content')
<x-encabezado eyebrow="Proveedores" :titulo="$proveedor->exists ? 'Editar proveedor' : 'Registrar proveedor'" subtitulo="Captura los datos fiscales y de contacto. El RFC y el correo deben ser únicos.">
    <a class="btn btn-outline" href="{{ route('panel.proveedores.index') }}">← Volver</a>
</x-encabezado>

<form class="card card-pad js-validate" method="POST" enctype="multipart/form-data"
      action="{{ $proveedor->exists ? route('panel.proveedores.update', $proveedor) : route('panel.proveedores.store') }}">
    @csrf
    @if ($proveedor->exists) @method('PUT') @endif
    <div class="form-grid">
        <x-campo name="razon_social" label="Razón social" :value="$proveedor->razon_social" required minlength="3" maxlength="150" />
        <x-campo name="rfc" label="RFC" :value="$proveedor->rfc" required maxlength="13" pattern="[A-Za-zÑñ&]{3,4}\d{6}[A-Za-z0-9]{3}" patron="Formato de RFC inválido (ej. ABC260101XY1)." hint="12 caracteres (moral) o 13 (física)." style="text-transform:uppercase" />
        <x-campo name="contacto" label="Nombre de contacto" :value="$proveedor->contacto" required minlength="3" maxlength="100" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\.]+" patron="Solo letras y espacios." />
        <x-campo name="email" label="Correo electrónico" type="email" :value="$proveedor->email" required maxlength="150" />
        <x-campo name="telefono" label="Teléfono" type="tel" :value="$proveedor->telefono" required pattern="\d{10}" maxlength="10" patron="Escribe 10 dígitos sin espacios." />
        <x-campo name="sitio_web" label="Sitio web" type="url" :value="$proveedor->sitio_web" maxlength="150" placeholder="https://" />
        <x-campo name="direccion" label="Dirección" :value="$proveedor->direccion" maxlength="255" />
        <x-campo name="ciudad" label="Ciudad" :value="$proveedor->ciudad" maxlength="80" />
        <x-campo name="calificacion" label="Calificación" type="select" :value="$proveedor->calificacion" required
                 :options="[5 => '★★★★★ Excelente', 4 => '★★★★ Muy bueno', 3 => '★★★ Bueno', 2 => '★★ Regular', 1 => '★ Deficiente']" />
        <div class="field" style="align-self:end">
            <label class="switch"><input type="checkbox" name="activo" value="1" @checked(old('activo', $proveedor->activo))> Proveedor activo</label>
        </div>
        <x-archivo name="logo" label="Logotipo" :actual="$proveedor->logo" :full="false" />
        <x-archivo name="constancia_fiscal" label="Constancia de situación fiscal" accept="application/pdf,.pdf" tipos="Solo PDF" max-mb="5"
                   :actual="$proveedor->constancia_fiscal" :actual-es-imagen="false" :full="false" />
    </div>
    <div class="form-actions">
        <a class="btn btn-outline" href="{{ route('panel.proveedores.index') }}">Cancelar</a>
        <button class="btn btn-yellow" type="submit"><i data-lucide="save"></i> Guardar</button>
    </div>
</form>
@endsection
