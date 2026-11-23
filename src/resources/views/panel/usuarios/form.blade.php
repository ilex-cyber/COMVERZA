@extends('layouts.panel')
@section('title', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')

@php $asignados = old('roles', $usuario->exists ? $usuario->getRoleNames()->all() : []); @endphp

@section('content')
<x-encabezado eyebrow="Usuarios" :titulo="$usuario->exists ? 'Editar '.$usuario->name : 'Nuevo usuario'"
    :subtitulo="$usuario->exists ? 'Deja la contraseña vacía para conservar la actual.' : 'Si dejas la contraseña vacía, el usuario recibirá un correo para definirla él mismo.'">
    <a class="btn btn-outline" href="{{ route('panel.usuarios.index') }}">← Volver</a>
</x-encabezado>

<form class="js-validate" method="POST" enctype="multipart/form-data" action="{{ $usuario->exists ? route('panel.usuarios.update', $usuario) : route('panel.usuarios.store') }}">
    @csrf
    @if ($usuario->exists) @method('PUT') @endif
    <div class="grid grid-3">
        <div class="card card-pad span-2">
            <div class="form-grid">
                <x-campo name="name" label="Nombre completo" :value="$usuario->name" required minlength="3" maxlength="100" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\.]+" patron="Solo letras y espacios." />
                <x-campo name="email" label="Correo electrónico" type="email" :value="$usuario->email" required maxlength="150" />
                <x-campo name="telefono" label="Teléfono" type="tel" :value="$usuario->telefono" pattern="\d{10}" maxlength="10" patron="Escribe 10 dígitos." />
                <x-campo name="puesto" label="Puesto" :value="$usuario->puesto" maxlength="100" />
                <div class="field">
                    <label for="f_password">Contraseña</label>
                    <div class="pw-wrap"><input id="f_password" type="password" name="password" minlength="8" maxlength="100" autocomplete="new-password"
                        pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}" data-nombre="La contraseña" data-patron="Debe incluir mayúsculas, minúsculas y números." data-password-meter>
                        <button type="button" data-toggle-password>Ver</button></div>
                    <div class="pw-meter"><span></span></div>
                    <div class="hint">Mínimo 8 caracteres, mayúsculas, minúsculas y números.</div>
                    @error('password')<div class="error">⚠ {{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="f_pc">Confirmar contraseña</label>
                    <input id="f_pc" type="password" name="password_confirmation" data-nombre="La confirmación" autocomplete="new-password">
                </div>
                <x-archivo name="avatar" label="Foto de perfil" :actual="$usuario->avatar" max-mb="1" />
                <div class="field full"><label class="switch"><input type="checkbox" name="activo" value="1" @checked(old('activo', $usuario->activo))> Cuenta activa (puede iniciar sesión)</label></div>
            </div>
        </div>
        <div class="card card-pad">
            <h3 style="color:var(--navy);margin-bottom:4px">Roles <span class="req" style="color:var(--red)">*</span></h3>
            <p style="color:var(--muted);font-size:13px;margin-bottom:14px">Asigna al menos uno. Los permisos se heredan del rol.</p>
            @foreach ($roles as $r)
                <label class="perm-box" style="display:flex;gap:10px;align-items:flex-start;margin-bottom:10px;cursor:pointer">
                    <input type="checkbox" name="roles[]" value="{{ $r->name }}" @checked(in_array($r->name, $asignados)) style="accent-color:var(--blue);width:18px;height:18px;margin-top:2px">
                    <span><b style="color:var(--navy)">{{ $r->name }}</b><br><small style="color:var(--muted)">{{ $r->permissions()->count() }} permisos</small></span>
                </label>
            @endforeach
            @error('roles')<div class="error" style="color:var(--red);font-weight:600">⚠ {{ $message }}</div>@enderror
            <div class="form-actions"><button class="btn btn-yellow" type="submit" style="width:100%"><i data-lucide="save"></i> Guardar usuario</button></div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form.js-validate');
    form.addEventListener('submit', e => {
        if (!form.querySelector('[name="roles[]"]:checked')) {
            e.preventDefault(); e.stopImmediatePropagation();
            Swal.fire({ icon: 'warning', title: 'Falta el rol', text: 'Asigna al menos un rol al usuario.', confirmButtonColor: '#1677ff' });
        }
    }, true);
});
</script>
@endpush
