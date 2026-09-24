<!DOCTYPE html>
<html lang="es">

@if (request()->routeIs('home'))
    <link rel="prefetch" href="{{ route('servicios') }}">
    <link rel="prefetch" href="{{ route('plataforma') }}">
    <link rel="prefetch" href="{{ route('contacto') }}">
@endif

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="COMVERZA - Tecnología que impulsa tu negocio."
    >

    <title>
        @yield('title', 'COMVERZA')
    </title>

    <link rel="stylesheet" href="{{ asset('css/comverza.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sections.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    @yield('styles')

</head>


<body>

    <!-- =========================
         NAVBAR
    ========================== -->

    <header class="navbar">

        <div class="container navbar-content">

            <a
                href="{{ route('home') }}"
                class="logo"
            >

                <img
                    src="{{ asset('images/logo_comverza.png') }}"
                    alt="Logo COMVERZA"
                >

            </a>


            <nav class="nav-menu">
                <a href="{{ route('home') }}"
                class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    Inicio
                </a>

                <a href="{{ route('home') }}#nosotros">
                    Nosotros
                </a>

                <a href="{{ route('servicios') }}"
                class="{{ request()->routeIs('servicios') ? 'active' : '' }}">
                    Servicios
                </a>

                <a href="{{ route('home') }}#productos">
                    Productos
                </a>

                <a href="{{ route('plataforma') }}"
                class="{{ request()->routeIs('plataforma') ? 'active' : '' }}">
                    Plataforma
                </a>

                <a href="{{ route('contacto') }}"
                class="{{ request()->routeIs('contacto') ? 'active' : '' }}">
                    Contacto
                </a>
            </nav>


            <div class="nav-actions">

                <a
                    href="#"
                    class="login-link"
                >
                    Iniciar sesión
                </a>

                <a
                    href="{{ route('contacto') }}"
                    class="btn btn-yellow"
                >
                    Comenzar ahora
                </a>

            </div>

        </div>

    </header>


    <main>

        @yield('content')

    </main>

    <!-- =========================
         FOOTER
    ========================== -->

    <footer class="footer">

        <div class="container footer-content">

            <div class="footer-brand">

                <a href="{{ route('home') }}">

                    <img
                        src="{{ asset('images/logo_comverza.png') }}"
                        alt="COMVERZA"
                    >

                </a>

                <p>
                    Tecnología que impulsa tu negocio.
                </p>

            </div>


            <div class="footer-column">

                <h4>
                    Empresa
                </h4>

                <a href="{{ route('home') }}#nosotros">
                    Nosotros
                </a>

                <a href="{{ route('servicios') }}">
                    Servicios
                </a>

                <a href="{{ route('home') }}#productos">
                    Productos
                </a>

            </div>


            <div class="footer-column">

                <h4>
                    Plataforma
                </h4>

                <a href="{{ route('home') }}#productos">
                    Módulos
                </a>

                <a href="{{ route('plataforma') }}">
                    Plataforma
                </a>

                <a href="{{ route('contacto') }}">
                    Soporte
                </a>

            </div>


            <div class="footer-column">

                <h4>
                    Contacto
                </h4>

                <a href="{{ route('contacto') }}">
                    Ponte en contacto
                </a>

                <a href="{{ route('contacto') }}">
                    Atención empresarial
                </a>

                <a href="{{ route('contacto') }}">
                    Soporte
                </a>

            </div>

        </div>


        <div class="footer-bottom">

            <div class="container footer-bottom-content">

                <span>
                    © {{ date('Y') }} COMVERZA.
                    Todos los derechos reservados.
                </span>

                <div>

                    <a href="#">
                        Privacidad
                    </a>

                    <a href="#">
                        Términos
                    </a>

                </div>

            </div>

        </div>

    </footer>

    <script
        src="{{ asset('js/comverza.js') }}"
    ></script>

</body>

</html>