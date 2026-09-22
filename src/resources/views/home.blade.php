<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>COMVERZA | Tecnología que impulsa tu negocio</title>

    <link rel="stylesheet" href="{{ asset('css/comverza.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sections.css') }}">
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
                <a href="#servicios">Servicios</a>
                <a href="#productos">Productos</a>
                <a href="#plataforma">Plataforma</a>
                <a href="#contacto">Contacto</a>

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

                    <div class="visual-glow"></div>

                    <div class="dashboard">

                        <div class="dashboard-header">

                            <div class="window-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>

                            <strong>
                                COMVERZA
                            </strong>

                            <small>
                                ● En línea
                            </small>

                        </div>


                        <div class="dashboard-body">

                            <aside class="dashboard-sidebar">

                                <div class="dashboard-logo">
                                    C
                                </div>

                                <div class="dashboard-item active">
                                    Inicio
                                </div>

                                <div class="dashboard-item">
                                    Productos
                                </div>

                                <div class="dashboard-item">
                                    Pedidos
                                </div>

                                <div class="dashboard-item">
                                    Proveedores
                                </div>

                                <div class="dashboard-item">
                                    Facturación
                                </div>

                            </aside>


                            <div class="dashboard-main">

                                <div class="dashboard-title">

                                    <div>
                                        <small>
                                            RESUMEN
                                        </small>

                                        <h3>
                                            Todo conectado.
                                        </h3>
                                    </div>

                                    <div class="avatar">
                                        C
                                    </div>

                                </div>


                                <div class="stats">

                                    <div class="stat-card">
                                        <span>📦</span>
                                        <strong>48</strong>
                                        <small>Productos</small>
                                    </div>

                                    <div class="stat-card">
                                        <span>🛒</span>
                                        <strong>24</strong>
                                        <small>Pedidos</small>
                                    </div>

                                    <div class="stat-card">
                                        <span>▤</span>
                                        <strong>18</strong>
                                        <small>Facturas</small>
                                    </div>

                                </div>


                                <div class="chart">

                                    <div class="chart-header">

                                        <strong>
                                            Actividad reciente
                                        </strong>

                                        <small>
                                            Últimos 7 días
                                        </small>

                                    </div>

                                    <div class="bars">

                                        <span style="height:35%"></span>
                                        <span style="height:50%"></span>
                                        <span style="height:42%"></span>
                                        <span style="height:70%"></span>
                                        <span style="height:57%"></span>
                                        <span style="height:85%"></span>
                                        <span style="height:72%"></span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- TARJETA FLOTANTE -->

                    <div class="floating-card floating-one">

                        <span class="floating-icon">
                            ✓
                        </span>

                        <div>
                            <strong>
                                Procesos conectados
                            </strong>

                            <small>
                                Todo bajo control
                            </small>
                        </div>

                    </div>


                    <div class="floating-card floating-two">

                        <span class="floating-icon yellow-icon">
                            ✦
                        </span>

                        <div>
                            <strong>
                                Mejores decisiones
                            </strong>

                            <small>
                                Información en tiempo real
                            </small>
                        </div>

                    </div>

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