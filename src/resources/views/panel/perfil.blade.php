@extends('layouts.panel')
@section('title', 'Mi perfil')

@section('content')
<x-encabezado eyebrow="Cuenta" titulo="Mi perfil" subtitulo="Actualiza tus datos y tu contraseña." />

<div class="grid grid-3">
    <form class="card card-pad span-2 js-validate" method="POST" action="{{ route('panel.perfil.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <h3 style="color:var(--navy);margin-bottom:16px">Datos personales</h3>
        <div class="form-grid">
            <x-campo name="name" label="Nombre" :value="$user->name" required minlength="3" maxlength="100" />
            <x-campo name="email" label="Correo" type="email" :value="$user->email" required maxlength="150" />
            <x-campo name="telefono" label="Teléfono" type="tel" :value="$user->telefono" pattern="\d{10}" maxlength="10" patron="Escribe 10 dígitos." />
            <x-campo name="puesto" label="Puesto" :value="$user->puesto" maxlength="100" />
            <x-archivo name="avatar" label="Foto de perfil" :actual="$user->avatar" max-mb="1" />
        </div>
        <div class="form-actions"><button class="btn btn-yellow" type="submit"><i data-lucide="save"></i> Guardar datos</button></div>
    </form>

    <div class="grid" style="align-content:start">
        <form class="card card-pad js-validate" method="POST" action="{{ route('panel.perfil.password') }}">
            @csrf @method('PUT')
            <h3 style="color:var(--navy);margin-bottom:16px">Cambiar contraseña</h3>
            <div class="field" style="margin-bottom:14px"><label>Contraseña actual <span class="req">*</span></label>
                <input type="password" name="password_actual" required data-nombre="La contraseña actual" autocomplete="current-password">
                @error('password_actual')<div class="error">⚠ {{ $message }}</div>@enderror</div>
            <div class="field" style="margin-bottom:14px"><label>Nueva contraseña <span class="req">*</span></label>
                <input type="password" name="password" required minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}" data-patron="Debe incluir mayúsculas, minúsculas y números." data-nombre="La contraseña" data-password-meter autocomplete="new-password">
                <div class="pw-meter"><span></span></div><div class="hint">Mínimo 8 caracteres.</div></div>
            <div class="field" style="margin-bottom:14px"><label>Confirmar <span class="req">*</span></label>
                <input type="password" name="password_confirmation" required data-nombre="La confirmación"></div>
            <button class="btn btn-blue" type="submit" style="width:100%">Actualizar contraseña</button>
        </form>
        <div class="card card-pad">
            <h3 style="color:var(--navy);margin-bottom:8px">Mis roles y permisos</h3>
            @foreach ($user->getRoleNames() as $r)<span class="badge badge-purple">{{ $r }}</span> @endforeach
            <div style="margin-top:12px">@foreach ($permisos as $p)<span class="chip">{{ $p }}</span>@endforeach</div>
        </div>
    </div>
</div>
@endsection
