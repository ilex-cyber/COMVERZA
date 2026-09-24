<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>COMVERZA | Tecnología que impulsa tu negocio</title>

    <link rel="stylesheet" href="{{ asset('css/comverza.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sections.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

    <!-- =========================
         NAVBAR
    ========================== -->
    <header class="navbar">

        <div class="container navbar-content">

            <a href="#" class="logo">
                <img
                    src="{{ asset('images/logo_comverza.png') }}"
                    alt="Logo COMVERZA"
                >
            </a>

            <nav class="nav-menu">

                <a href="#">Inicio</a>
                <a href="#nosotros">Nosotros</a>
                <a href="{{ route('servicios') }}">
                    Servicios
                </a>
                <a href="#productos">Productos</a>
                <a href="{{ route('plataforma') }}">
                    Plataforma
                </a>

                <a href="{{ route('contacto') }}">
                    Contacto
                </a>

            </nav>

            <div class="nav-actions">

                <a href="#login" class="login-link">
                    Iniciar sesión
                </a>

                <a href="#contacto" class="btn btn-yellow">
                    Comenzar ahora
                </a>

            </div>

        </div>

    </header>


    <!-- =========================
         HERO
    ========================== -->
    <main>

        <section class="hero">

            <!-- Efectos de fondo -->

            <div class="hero-glow glow-one"></div>
            <div class="hero-glow glow-two"></div>
            <div class="hero-grid"></div>


            <div class="container hero-content">

                <!-- TEXTO -->

                <div class="hero-text reveal">

                    <span class="hero-badge">
                        <span></span>
                        Plataforma empresarial
                    </span>

                    <h1>
                        Conecta, centraliza y
                        <strong>
                            gestiona tu negocio
                        </strong>
                        desde un solo lugar.
                    </h1>

                    <p>
                        COMVERZA es una plataforma empresarial diseñada
                        para conectar empresas, clientes y proveedores
                        en un entorno centralizado, seguro y eficiente.
                    </p>

                    <div class="hero-buttons">

                        <a href="#plataforma" class="btn btn-yellow">
                            Explorar plataforma
                            <span>→</span>
                        </a>

                        <a href="#nosotros" class="btn btn-outline">
                            Conocer COMVERZA
                        </a>

                    </div>

                </div>


                <!-- VISUAL -->

                <div class="hero-visual reveal">

                    @include('components.comverza-dashboard')

                </div>

            </div>

        </section>

        <!-- =========================
             INDICADORES
        ========================== -->

        <section class="stats-section">

            <div class="container stats-grid">

                <article class="stat-item reveal">

                    <div class="stat-icon">
                        ◈
                    </div>

                    <div>
                        <strong>Productos</strong>
                        <span>Catálogo e inventario</span>
                    </div>

                </article>


                <article class="stat-item reveal">

                    <div class="stat-icon">
                        👥
                    </div>

                    <div>
                        <strong>Empresas</strong>
                        <span>Operaciones conectadas</span>
                    </div>

                </article>


                <article class="stat-item reveal">

                    <div class="stat-icon">
                        ◇
                    </div>

                    <div>
                        <strong>Procesos</strong>
                        <span>Gestión centralizada</span>
                    </div>

                </article>


                <article class="stat-item reveal">

                    <div class="stat-icon">
                        ◉
                    </div>

                    <div>
                        <strong>Soporte</strong>
                        <span>Acompañamiento continuo</span>
                    </div>

                </article>

            </div>

        </section>

        <!-- =========================
             ¿QUÉ ES COMVERZA?
        ========================== -->

        <section class="about-section" id="nosotros">

            <div class="container about-grid">


                <!-- IMAGEN FUTURA -->

                <div class="about-image reveal">
                    <img 
                        src="{{ asset('images/que_somos_comverza.png') }}"
                        alt="COMVERZA - Plataforma empresarial"
                    >
                </div>


                <!-- INFORMACIÓN -->

                <div class="about-content reveal">

                    <span class="section-label">
                        ¿QUÉ ES COMVERZA?
                    </span>


                    <h2>
                        Una visión para
                        <span>
                            centralizar la gestión empresarial.
                        </span>
                    </h2>


                    <p>
                        COMVERZA es una plataforma empresarial
                        diseñada para conectar procesos, empresas,
                        clientes y proveedores dentro de un mismo
                        entorno digital.
                    </p>


                    <div class="benefits">


                        <div class="benefit">

                            <div class="benefit-icon">
                                ✓
                            </div>

                            <div>
                                <strong>
                                    Más eficiencia
                                </strong>

                                <span>
                                    Simplifica tus procesos.
                                </span>
                            </div>

                        </div>


                        <div class="benefit">

                            <div class="benefit-icon">
                                ✓
                            </div>

                            <div>
                                <strong>
                                    Mayor control
                                </strong>

                                <span>
                                    Ten una visión más clara.
                                </span>
                            </div>

                        </div>


                        <div class="benefit">

                            <div class="benefit-icon">
                                ✓
                            </div>

                            <div>
                                <strong>
                                    Más conexión
                                </strong>

                                <span>
                                    Mantén todo conectado.
                                </span>
                            </div>

                        </div>


                    </div>

                </div>

            </div>

        </section>

        <!-- =========================
             MODULOS DE LA PLATAFORMA
        ========================== -->

        <section class="modules-section" id="productos">

            <div class="container">

                <div class="modules-heading reveal">

                    <div>

                        <span class="section-label">
                            MÓDULOS DE LA PLATAFORMA
                        </span>

                        <h2>
                            Todo lo que necesitas
                            <span>
                                en un solo lugar.
                            </span>
                        </h2>

                    </div>

                    <p>
                        Cada módulo está diseñado para simplificar
                        tus procesos y ayudarte a mantener tu negocio conectado.
                    </p>

                </div>


                <div class="modules-grid">


                    <!-- PRODUCTOS -->

                    <article class="module-card reveal">

                        <div class="module-icon">
                            ◈
                        </div>

                        <div class="module-content">

                            <h3>
                                Productos
                            </h3>

                            <p>
                                Catálogo y control de inventario.
                            </p>

                        </div>

                        <a href="#">
                            →
                        </a>

                    </article>


                    <!-- PEDIDOS -->

                    <article class="module-card reveal">

                        <div class="module-icon">
                            🛒
                        </div>

                        <div class="module-content">

                            <h3>
                                Pedidos
                            </h3>

                            <p>
                                Seguimiento y gestión comercial.
                            </p>

                        </div>

                        <a href="#">
                            →
                        </a>

                    </article>


                    <!-- PROVEEDORES -->

                    <article class="module-card reveal">

                        <div class="module-icon">
                            👥
                        </div>

                        <div class="module-content">

                            <h3>
                                Proveedores
                            </h3>

                            <p>
                                Red de proveedores y relaciones comerciales.
                            </p>

                        </div>

                        <a href="#">
                            →
                        </a>

                    </article>


                    <!-- FACTURACIÓN -->

                    <article class="module-card reveal">

                        <div class="module-icon">
                            ▤
                        </div>

                        <div class="module-content">

                            <h3>
                                Facturación
                            </h3>

                            <p>
                                Documentos y control administrativo.
                            </p>

                        </div>

                        <a href="#">
                            →
                        </a>

                    </article>


                </div>

            </div>

        </section>

        <!-- =========================
            FOOTER
        ========================== -->

        <footer class="footer" id="contacto">

            <div class="container footer-content">


                <!-- MARCA -->

                <div class="footer-brand">

                    <a href="#">

                        <img
                            src="{{ asset('images/logo_comverza.png') }}"
                            alt="COMVERZA"
                        >

                    </a>

                    <p>
                        Tecnología que impulsa tu negocio.
                    </p>

                </div>


                <!-- NAVEGACIÓN -->

                <div class="footer-column">

                    <h4>
                        Empresa
                    </h4>

                    <a href="#nosotros">
                        Nosotros
                    </a>

                    <a href="#servicios">
                        Servicios
                    </a>

                    <a href="#productos">
                        Productos
                    </a>

                </div>


                <!-- PLATAFORMA -->

                <div class="footer-column">

                    <h4>
                        Plataforma
                    </h4>

                    <a href="#productos">
                        Productos
                    </a>

                    <a href="#productos">
                        Pedidos
                    </a>

                    <a href="#productos">
                        Proveedores
                    </a>

                    <a href="#productos">
                        Facturación
                    </a>

                </div>


                <!-- CONTACTO -->

                <div class="footer-column">

                    <h4>
                        Contacto
                    </h4>

                    <a href="#">
                        contacto@comverza.com
                    </a>

                    <a href="#">
                        Atención empresarial
                    </a>

                    <a href="#">
                        Soporte
                    </a>

                </div>


            </div>


            <!-- COPYRIGHT -->

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
    </main>

    <script src="{{ asset('js/comverza.js') }}"></script>

</body>
</html>