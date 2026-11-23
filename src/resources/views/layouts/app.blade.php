<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="COMVERZA - Plataforma empresarial para conectar empresas, clientes y proveedores.">
    <title>@yield('title', 'COMVERZA | Tecnología que impulsa tu negocio')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    @if (request()->routeIs('home'))
        <link rel="prefetch" href="{{ route('servicios') }}">
        <link rel="prefetch" href="{{ route('catalogo') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/comverza.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sections.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=3">
    @yield('styles')
</head>

<body>
    <header class="navbar">
        <div class="container navbar-content">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ asset('images/logo_comverza.png') }}" alt="Logo COMVERZA">
            </a>

            <button class="nav-toggle" type="button" aria-label="Abrir menú" data-nav-toggle>
                <span></span><span></span><span></span>
            </button>

            <nav class="nav-menu" data-nav>
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a>
                <a href="{{ route('home') }}#nosotros">Nosotros</a>
                <a href="{{ route('servicios') }}" class="{{ request()->routeIs('servicios') ? 'active' : '' }}">Servicios</a>
                <a href="{{ route('catalogo') }}" class="{{ request()->routeIs('catalogo') ? 'active' : '' }}">Catálogo</a>
                <a href="{{ route('plataforma') }}" class="{{ request()->routeIs('plataforma') ? 'active' : '' }}">Plataforma</a>
                <a href="{{ route('contacto') }}" class="{{ request()->routeIs('contacto') ? 'active' : '' }}">Contacto</a>
            </nav>

            <div class="nav-actions">
                @auth
                    <a href="{{ route('panel.dashboard') }}" class="login-link">Mi panel</a>
                @else
                    <a href="{{ route('login') }}" class="login-link">Iniciar sesión</a>
                @endauth
                <a href="{{ route('contacto') }}" class="btn btn-yellow">Comenzar ahora</a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-brand">
                <a href="{{ route('home') }}"><img src="{{ asset('images/logo_comverza.png') }}" alt="COMVERZA"></a>
                <p>Tecnología que impulsa tu negocio.</p>
            </div>
            <div class="footer-column">
                <h4>Empresa</h4>
                <a href="{{ route('home') }}#nosotros">Nosotros</a>
                <a href="{{ route('servicios') }}">Servicios</a>
                <a href="{{ route('catalogo') }}">Catálogo</a>
            </div>
            <div class="footer-column">
                <h4>Plataforma</h4>
                <a href="{{ route('plataforma') }}">Módulos</a>
                <a href="{{ route('login') }}">Acceso clientes</a>
                <a href="{{ url('/api/v1') }}" target="_blank">API pública</a>
            </div>
            <div class="footer-column">
                <h4>Contacto</h4>
                <a href="mailto:contacto@comverza.com">contacto@comverza.com</a>
                <a href="{{ route('contacto') }}">Atención empresarial</a>
                <a href="{{ route('contacto') }}">Soporte</a>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container footer-bottom-content">
                <span>© {{ date('Y') }} COMVERZA. Todos los derechos reservados.</span>
                <div><a href="{{ route('contacto') }}">Privacidad</a><a href="{{ route('contacto') }}">Términos</a></div>
            </div>
        </div>
    </footer>

    @stack('scripts')
    <script src="{{ asset('js/comverza.js') }}?v=3"></script>
</body>
</html>
