@extends('layouts.auth')
@section('title', 'Recuperar contraseña')
@section('content')
    <h1>¿Olvidaste tu contraseña?</h1>
    <p>Escribe tu correo y te enviaremos un enlace seguro para crear una nueva. El enlace vence en 60 minutos.</p>

    <form method="POST" action="{{ route('password.email') }}" class="js-validate">
        @csrf
        <x-campo name="email" label="Correo electrónico" type="email" required maxlength="150" placeholder="tu@empresa.com" />
        <button class="btn btn-yellow" type="submit">Enviar enlace de recuperación</button>
    </form>
    <p style="margin-top:20px;text-align:center"><a class="link" href="{{ route('login') }}">← Volver a iniciar sesión</a></p>
@endsection
