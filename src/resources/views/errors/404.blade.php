<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada · COMVERZA</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="error-page">
    <div>
        <h1>404</h1>
        <h2>No encontramos esta página</h2>
        <p>Es posible que el enlace haya cambiado o que el registro ya no exista.</p>
        <a class="btn btn-yellow" href="{{ route('home') }}">Ir al inicio</a>
    </div>
</div>
</body>
</html>
