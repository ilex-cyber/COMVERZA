@extends('layouts.auth')
@section('title', 'Nueva contraseña')
@section('content')
    <h1>Crea tu nueva contraseña</h1>
    <p>Usa al menos 8 caracteres combinando mayúsculas, minúsculas y números.</p>

    <form method="POST" action="{{ route('password.update') }}" class="js-validate">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-campo name="email" label="Correo electrónico" type="email" :value="$email" required maxlength="150" />
        <div class="field {{ $errors->has('password') ? 'has-error' : '' }}">
            <label for="f_password">Nueva contraseña <span class="req">*</span></label>
            <div class="pw-wrap">
                <input id="f_password" type="password" name="password" required minlength="8" maxlength="100"
                       pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}" data-nombre="La contraseña"
                       data-patron="Debe incluir mayúsculas, minúsculas y números." data-password-meter>
                <button type="button" data-toggle-password>Ver</button>
            </div>
            <div class="pw-meter"><span></span></div>
            <div class="hint">Mínimo 8 caracteres, mayúsculas, minúsculas y números.</div>
            @error('password')<div class="error">⚠ {{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label for="f_pc">Confirmar contraseña <span class="req">*</span></label>
            <input id="f_pc" type="password" name="password_confirmation" required data-nombre="La confirmación">
        </div>
        <button class="btn btn-yellow" type="submit">Guardar contraseña</button>
    </form>
@endsection
