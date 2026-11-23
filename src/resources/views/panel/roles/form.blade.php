@extends('layouts.panel')
@section('title', $rol->exists ? 'Editar rol' : 'Nuevo rol')

@php
    $marcados = old('permisos', $asignados);
    $acciones = ['ver' => 'Ver', 'crear' => 'Crear', 'editar' => 'Editar', 'eliminar' => 'Eliminar', 'exportar' => 'Exportar'];
@endphp

@section('content')
<x-encabezado eyebrow="Roles" :titulo="$rol->exists ? 'Rol «'.$rol->name.'»' : 'Nuevo rol'" subtitulo="Marca los permisos que tendrá este rol en cada módulo.">
    <a class="btn btn-outline" href="{{ route('panel.roles.index') }}">← Volver</a>
</x-encabezado>

<form class="js-validate" method="POST" action="{{ $rol->exists ? route('panel.roles.update', $rol) : route('panel.roles.store') }}">
    @csrf
    @if ($rol->exists) @method('PUT') @endif
    <div class="card card-pad" style="margin-bottom:20px">
        <div class="form-grid">
            <x-campo name="name" label="Nombre del rol" :value="$rol->name" required minlength="3" maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" patron="Solo letras y espacios." />
            <div class="field" style="align-self:end;display:flex;gap:8px">
                <button type="button" class="btn btn-outline btn-sm" data-todos="1">Marcar todos</button>
                <button type="button" class="btn btn-outline btn-sm" data-todos="0">Quitar todos</button>
            </div>
        </div>
    </div>
    <div class="perm-grid">
        @foreach ($grupos as $modulo => $permisos)
            <div class="perm-box">
                <h4>{{ $modulo }} <input type="checkbox" title="Todo el módulo" data-modulo="{{ $modulo }}"></h4>
                @foreach ($permisos as $p)
                    @php $accion = explode('.', $p->name)[1] ?? $p->name; @endphp
                    <label><input type="checkbox" name="permisos[]" value="{{ $p->name }}" data-grupo="{{ $modulo }}" @checked(in_array($p->name, $marcados))> {{ $acciones[$accion] ?? $accion }}</label>
                @endforeach
            </div>
        @endforeach
    </div>
    <div class="card card-pad" style="margin-top:20px">
        <div class="form-actions" style="border:0;margin:0;padding:0">
            <a class="btn btn-outline" href="{{ route('panel.roles.index') }}">Cancelar</a>
            <button class="btn btn-yellow" type="submit"><i data-lucide="save"></i> Guardar rol</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const checks = [...document.querySelectorAll('[name="permisos[]"]')];
    const syncModulos = () => document.querySelectorAll('[data-modulo]').forEach(m => {
        const g = checks.filter(c => c.dataset.grupo === m.dataset.modulo);
        m.checked = g.every(c => c.checked); m.indeterminate = !m.checked && g.some(c => c.checked);
    });
    document.querySelectorAll('[data-modulo]').forEach(m => m.addEventListener('change', () => {
        checks.filter(c => c.dataset.grupo === m.dataset.modulo).forEach(c => c.checked = m.checked);
    }));
    document.querySelectorAll('[data-todos]').forEach(b => b.addEventListener('click', () => { checks.forEach(c => c.checked = b.dataset.todos === '1'); syncModulos(); }));
    checks.forEach(c => c.addEventListener('change', syncModulos));
    syncModulos();
});
</script>
@endpush
