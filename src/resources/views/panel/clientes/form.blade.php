@extends('layouts.panel')
@section('title', $cliente->exists ? 'Editar cliente' : 'Nuevo cliente')

@section('content')
<x-encabezado eyebrow="Clientes" :titulo="$cliente->exists ? 'Editar cliente' : 'Registrar cliente'" subtitulo="Para clientes de tipo Empresa, la razón social y el RFC son obligatorios.">
    <a class="btn btn-outline" href="{{ route('panel.clientes.index') }}">← Volver</a>
</x-encabezado>

<form class="card card-pad js-validate" method="POST" action="{{ $cliente->exists ? route('panel.clientes.update', $cliente) : route('panel.clientes.store') }}">
    @csrf
    @if ($cliente->exists) @method('PUT') @endif
    <div class="form-grid">
        <x-campo name="tipo" label="Tipo de cliente" type="select" :value="$cliente->tipo" required :options="['Empresa' => 'Empresa', 'Persona' => 'Persona física']" id="tipoCliente" />
        <x-campo name="nombre" label="Nombre del contacto" :value="$cliente->nombre" required minlength="3" maxlength="120" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\.]+" patron="Solo letras y espacios." />
        <x-campo name="empresa" label="Empresa / razón social" :value="$cliente->empresa" maxlength="150" class="solo-empresa" />
        <x-campo name="rfc" label="RFC" :value="$cliente->rfc" maxlength="13" pattern="[A-Za-zÑñ&]{3,4}\d{6}[A-Za-z0-9]{3}" patron="Formato de RFC inválido (ej. ABC260101XY1)." class="solo-empresa" />
        <x-campo name="email" label="Correo electrónico" type="email" :value="$cliente->email" required maxlength="150" />
        <x-campo name="telefono" label="Teléfono" type="tel" :value="$cliente->telefono" required pattern="\d{10}" maxlength="10" patron="Escribe 10 dígitos sin espacios." />
        <x-campo name="direccion" label="Dirección" :value="$cliente->direccion" maxlength="255" full />
        <x-campo name="ciudad" label="Ciudad" :value="$cliente->ciudad" maxlength="80" />
        <x-campo name="codigo_postal" label="Código postal" :value="$cliente->codigo_postal" pattern="\d{5}" maxlength="5" patron="El código postal tiene 5 dígitos." />
        <x-campo name="limite_credito" label="Límite de crédito" type="number" :value="$cliente->limite_credito" required min="0" max="9999999" step="0.01" prefix="$" />
        <div class="field" style="align-self:end">
            <label class="switch"><input type="checkbox" name="activo" value="1" @checked(old('activo', $cliente->activo))> Cliente activo</label>
        </div>
    </div>
    <div class="form-actions">
        <a class="btn btn-outline" href="{{ route('panel.clientes.index') }}">Cancelar</a>
        <button class="btn btn-yellow" type="submit"><i data-lucide="save"></i> Guardar</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Empresa y RFC son obligatorios solo para clientes de tipo Empresa
document.addEventListener('DOMContentLoaded', () => {
    const tipo = document.querySelector('[name=tipo]');
    const sync = () => document.querySelectorAll('.solo-empresa').forEach(f => {
        const es = tipo.value === 'Empresa';
        f.style.display = es ? '' : 'none';
        const input = f.querySelector('input');
        input.required = es;
        if (!es) input.value = '';
        const lbl = f.querySelector('label');
        lbl.querySelector('.req')?.remove();
        if (es) lbl.insertAdjacentHTML('beforeend', ' <span class="req">*</span>');
    });
    tipo.addEventListener('change', sync); sync();
});
</script>
@endpush
