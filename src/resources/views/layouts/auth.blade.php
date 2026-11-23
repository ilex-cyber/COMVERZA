<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') · COMVERZA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=3">
</head>
<body class="auth">
<div class="auth-shell">
    <section class="auth-visual">
        <a href="{{ route('home') }}"><img class="logo" src="{{ asset('images/logo_comverza.png') }}" alt="COMVERZA"></a>
        <div style="position:relative;z-index:1">
            <h2>Conecta, centraliza y <span>gestiona tu negocio.</span></h2>
            <p>Productos, pedidos, clientes y proveedores en una sola plataforma segura, con roles y permisos para cada integrante de tu equipo.</p>
        </div>
        <div class="auth-points">
            <div><strong>RBAC</strong>Roles y permisos</div>
            <div><strong>PDF · Excel</strong>Reportes al instante</div>
            <div><strong>API REST</strong>Integraciones abiertas</div>
        </div>
    </section>
    <section class="auth-form">
        <div class="auth-card">
            @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @yield('content')
        </div>
    </section>
</div>
<script src="{{ asset('js/panel.js') }}?v=3"></script>
</body>
</html>
