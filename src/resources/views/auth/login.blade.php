@extends('layouts.auth')
@section('title', 'Iniciar sesión')
@section('content')
    <h1>Iniciar sesión</h1>
    <p>Accede al panel de COMVERZA con tu cuenta empresarial.</p>

    <form method="POST" action="{{ route('login.intento') }}" class="js-validate">
        @csrf
        <x-campo name="email" label="Correo electrónico" type="email" required maxlength="150" placeholder="tu@empresa.com" />
        <div class="field {{ $errors->has('password') ? 'has-error' : '' }}">
            <label for="f_password">Contraseña <span class="req">*</span></label>
            <div class="pw-wrap">
                <input id="f_password" type="password" name="password" required maxlength="100" data-nombre="La contraseña">
                <button type="button" data-toggle-password>Ver</button>
            </div>
            @error('password')<div class="error">⚠ {{ $message }}</div>@enderror
        </div>
        <div class="auth-row">
            <label class="switch"><input type="checkbox" name="recordar" value="1"> Recordarme</label>
            <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
        </div>
        <button class="btn btn-yellow" type="submit">Entrar al panel →</button>
    </form>

    <div class="demo-box">
        <strong>Cuentas de demostración</strong> (contraseña <code>Comverza2026!</code>):<br>
        <code>admin@comverza.com</code> · Administrador<br>
        <code>gerente@comverza.com</code> · Gerente<br>
        <code>ventas@comverza.com</code> · Vendedor<br>
        <code>almacen@comverza.com</code> · Almacenista
    </div>
@endsection
