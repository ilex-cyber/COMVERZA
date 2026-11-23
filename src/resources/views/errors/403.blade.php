<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso denegado · COMVERZA</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="error-page">
    <div>
        <h1>403</h1>
        <h2>Acceso denegado</h2>
        <p>{{ $mensaje ?? ($exception?->getMessage() ?: 'No tienes permiso para ver esta página.') }}</p>
        <a class="btn btn-yellow" href="{{ auth()->check() ? url('/panel/perfil') : route('home') }}">Volver</a>
    </div>
</div>
</body>
</html>
