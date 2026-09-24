@extends('layouts.app')

@section('title', 'Plataforma | COMVERZA')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/platform.css') }}">
@endsection

@section('content')

<div class="platform-page">

    {{-- =====================================================
         HERO
    ====================================================== --}}
    <section class="platform-hero">

        <div class="platform-container platform-hero-grid">

            <div class="platform-hero-copy">

                <span class="platform-eyebrow">
                    <i></i>
                    PLATAFORMA EMPRESARIAL
                </span>

                <h1>
                    Toda tu operación.
                    <strong>Un solo lugar.</strong>
                </h1>

                <p>
                    COMVERZA reúne productos, pedidos, proveedores,
                    facturación, reportes y seguimiento operativo
                    dentro de una misma experiencia.
                </p>

                <div class="platform-hero-actions">

                    <a href="#platform-dashboard" class="platform-btn platform-btn-primary">
                        Explorar plataforma
                        <span>→</span>
                    </a>

                    <a href="#platform-modules" class="platform-btn platform-btn-outline">
                        Ver módulos
                    </a>

                </div>

            </div>


            {{-- DASHBOARD PRINCIPAL --}}
            <div class="platform-hero-visual">

                @include('components.comverza-dashboard')

            </div>

        </div>

    </section>


    {{-- =====================================================
         DASHBOARD
    ====================================================== --}}
    <section class="platform-dashboard-section" id="platform-dashboard">

        <div class="platform-container">

            <div class="platform-section-heading">

                <div>

                    <span class="platform-section-label">
                        01 · DASHBOARD
                    </span>

                    <h2>
                        Una visión completa
                        <strong>de tu negocio.</strong>
                    </h2>

                </div>

                <p>
                    El panel principal concentra los indicadores,
                    procesos y movimientos más importantes para
                    consultar rápidamente el estado de la operación.
                </p>

            </div>


            {{-- TARJETAS UNIFORMES --}}
            <div class="platform-summary-grid">

                <article class="platform-summary-card">

                    <div class="platform-summary-top">
                        <span>VENTAS</span>
                        <i>↗</i>
                    </div>

                    <strong>$428,320</strong>

                    <small>
                        +14.8% respecto al periodo anterior
                    </small>

                    <div class="platform-mini-bars">
                        <i style="height: 30%"></i>
                        <i style="height: 42%"></i>
                        <i style="height: 36%"></i>
                        <i style="height: 54%"></i>
                        <i style="height: 65%"></i>
                        <i style="height: 78%"></i>
                        <i style="height: 91%"></i>
                    </div>

                </article>


                <article class="platform-summary-card">

                    <div class="platform-summary-top">
                        <span>PEDIDOS</span>
                        <i>↗</i>
                    </div>

                    <strong>386</strong>

                    <small>
                        42 actualmente en proceso
                    </small>

                    <div class="platform-progress">
                        <span style="width: 72%"></span>
                    </div>

                </article>


                <article class="platform-summary-card">

                    <div class="platform-summary-top">
                        <span>PRODUCTOS</span>
                        <i>↗</i>
                    </div>

                    <strong>1,248</strong>

                    <small>
                        Catálogo e inventario centralizados
                    </small>

                    <div class="platform-progress">
                        <span style="width: 84%"></span>
                    </div>

                </article>


                <article class="platform-summary-card">

                    <div class="platform-summary-top">
                        <span>PROVEEDORES</span>
                        <i>↗</i>
                    </div>

                    <strong>84</strong>

                    <small>
                        Relaciones comerciales activas
                    </small>

                    <div class="platform-progress">
                        <span style="width: 63%"></span>
                    </div>

                </article>

            </div>


            {{-- RESUMEN INFERIOR --}}
            <div class="platform-dashboard-bottom">

                <div class="platform-performance-card">

                    <div class="platform-card-heading">

                        <div>
                            <span>ANÁLISIS</span>
                            <strong>Actividad operativa</strong>
                        </div>

                        <button type="button">
                            Últimos 30 días
                        </button>

                    </div>


                    <div class="platform-large-chart">

                        <div class="platform-chart-grid-line"></div>
                        <div class="platform-chart-grid-line"></div>
                        <div class="platform-chart-grid-line"></div>
                        <div class="platform-chart-grid-line"></div>

                        <div class="platform-chart-columns">
                            <i style="height: 32%"></i>
                            <i style="height: 45%"></i>
                            <i style="height: 39%"></i>
                            <i style="height: 57%"></i>
                            <i style="height: 52%"></i>
                            <i style="height: 68%"></i>
                            <i style="height: 64%"></i>
                            <i style="height: 82%"></i>
                            <i style="height: 74%"></i>
                            <i style="height: 92%"></i>
                        </div>

                    </div>

                </div>


                <div class="platform-insights-card">

                    <div class="platform-card-heading">

                        <div>
                            <span>RESUMEN</span>
                            <strong>Indicadores</strong>
                        </div>

                    </div>


                    <div class="platform-insight">

                        <div>
                            <span>Pedidos completados</span>
                            <strong>92%</strong>
                        </div>

                        <div class="platform-insight-bar">
                            <span style="width: 92%"></span>
                        </div>

                    </div>


                    <div class="platform-insight">

                        <div>
                            <span>Productos disponibles</span>
                            <strong>84%</strong>
                        </div>

                        <div class="platform-insight-bar">
                            <span style="width: 84%"></span>
                        </div>

                    </div>


                    <div class="platform-insight">

                        <div>
                            <span>Procesos automatizados</span>
                            <strong>68%</strong>
                        </div>

                        <div class="platform-insight-bar">
                            <span style="width: 68%"></span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
         MODULOS
    ====================================================== --}}
    <section class="platform-modules-section" id="platform-modules">

        <div class="platform-container">

            <div class="platform-section-heading platform-heading-light">

                <div>

                    <span class="platform-section-label">
                        02 · MÓDULOS
                    </span>

                    <h2>
                        Cada área de tu operación,
                        <strong>conectada.</strong>
                    </h2>

                </div>

                <p>
                    COMVERZA organiza la plataforma en módulos
                    especializados para que cada proceso tenga
                    un espacio claro y centralizado.
                </p>

            </div>


            <div class="platform-modules-grid">

                {{-- PRODUCTOS --}}
                <article class="platform-module-card" id="platform-products">

                    <div class="platform-module-icon">
                        □
                    </div>

                    <div class="platform-module-number">
                        01
                    </div>

                    <h3>Productos</h3>

                    <p>
                        Administra el catálogo, inventario,
                        información comercial y publicación
                        de productos.
                    </p>

                    <div class="platform-module-tags">
                        <span>Catálogo</span>
                        <span>Inventario</span>
                        <span>Publicación</span>
                    </div>

                </article>


                {{-- PEDIDOS --}}
                <article class="platform-module-card" id="platform-orders">

                    <div class="platform-module-icon">
                        ↗
                    </div>

                    <div class="platform-module-number">
                        02
                    </div>

                    <h3>Pedidos</h3>

                    <p>
                        Publicación, gestión y seguimiento
                        de pedidos durante todo el proceso.
                    </p>

                    <div class="platform-module-tags">
                        <span>Publicación</span>
                        <span>Seguimiento</span>
                        <span>Estados</span>
                    </div>

                </article>


                {{-- PROVEEDORES --}}
                <article class="platform-module-card" id="platform-suppliers">

                    <div class="platform-module-icon">
                        ◎
                    </div>

                    <div class="platform-module-number">
                        03
                    </div>

                    <h3>Proveedores</h3>

                    <p>
                        Centraliza información y relaciones
                        comerciales con tus proveedores.
                    </p>

                    <div class="platform-module-tags">
                        <span>Directorio</span>
                        <span>Relaciones</span>
                        <span>Historial</span>
                    </div>

                </article>


                {{-- FACTURACIÓN --}}
                <article class="platform-module-card" id="platform-billing">

                    <div class="platform-module-icon">
                        ▤
                    </div>

                    <div class="platform-module-number">
                        04
                    </div>

                    <h3>Facturación</h3>

                    <p>
                        Organiza documentos, consulta registros
                        y controla la información de facturación.
                    </p>

                    <div class="platform-module-tags">
                        <span>Documentos</span>
                        <span>Control</span>
                        <span>Historial</span>
                    </div>

                </article>


                {{-- REPORTES --}}
                <article class="platform-module-card" id="platform-reports">

                    <div class="platform-module-icon">
                        ▥
                    </div>

                    <div class="platform-module-number">
                        05
                    </div>

                    <h3>Reportes</h3>

                    <p>
                        Consulta métricas, gráficas e indicadores
                        para analizar el comportamiento de la operación.
                    </p>

                    <div class="platform-module-tags">
                        <span>Gráficas</span>
                        <span>Métricas</span>
                        <span>Exportación</span>
                    </div>

                </article>


                {{-- CONFIGURACIÓN --}}
                <article class="platform-module-card">

                    <div class="platform-module-icon">
                        ⚙
                    </div>

                    <div class="platform-module-number">
                        06
                    </div>

                    <h3>Configuración</h3>

                    <p>
                        Administra usuarios, permisos y parámetros
                        generales de la plataforma.
                    </p>

                    <div class="platform-module-tags">
                        <span>Usuarios</span>
                        <span>Permisos</span>
                        <span>Sistema</span>
                    </div>

                </article>

            </div>

        </div>

    </section>


    {{-- =====================================================
         PEDIDOS
    ====================================================== --}}
    <section class="platform-orders-section" id="platform-order-flow">

        <div class="platform-container platform-orders-grid">

            <div class="platform-orders-copy">

                <span class="platform-section-label">
                    03 · PEDIDOS
                </span>

                <h2>
                    Publica, administra
                    y <strong>da seguimiento.</strong>
                </h2>

                <p>
                    La gestión de pedidos permitirá visualizar cada
                    etapa del proceso y mantener el control desde
                    la publicación hasta la entrega.
                </p>


                <div class="platform-order-process">

                    <div class="platform-order-step is-active">

                        <span>01</span>

                        <strong>Publicado</strong>

                        <small>
                            Pedido creado
                        </small>

                    </div>


                    <div class="platform-order-line"></div>


                    <div class="platform-order-step">

                        <span>02</span>

                        <strong>Procesando</strong>

                        <small>
                            En preparación
                        </small>

                    </div>


                    <div class="platform-order-line"></div>


                    <div class="platform-order-step">

                        <span>03</span>

                        <strong>Enviado</strong>

                        <small>
                            En tránsito
                        </small>

                    </div>


                    <div class="platform-order-line"></div>


                    <div class="platform-order-step">

                        <span>04</span>

                        <strong>Entregado</strong>

                        <small>
                            Finalizado
                        </small>

                    </div>

                </div>

            </div>


            <div class="platform-orders-card">

                <div class="platform-card-heading">

                    <div>
                        <span>ÚLTIMOS PEDIDOS</span>
                        <strong>Seguimiento</strong>
                    </div>

                    <a href="#platform-order-flow">
                        Ver todos →
                    </a>

                </div>


                <div class="platform-order-item">

                    <div>
                        <strong>#CV-1048</strong>
                        <small>Cliente empresarial</small>
                    </div>

                    <span class="platform-status processing">
                        Procesando
                    </span>

                </div>


                <div class="platform-order-item">

                    <div>
                        <strong>#CV-1047</strong>
                        <small>Cliente empresarial</small>
                    </div>

                    <span class="platform-status shipped">
                        Enviado
                    </span>

                </div>


                <div class="platform-order-item">

                    <div>
                        <strong>#CV-1046</strong>
                        <small>Cliente empresarial</small>
                    </div>

                    <span class="platform-status delivered">
                        Entregado
                    </span>

                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
         REPORTES
    ====================================================== --}}
    <section class="platform-reports-section" id="platform-reporting">

        <div class="platform-container platform-reports-grid">

            <div class="platform-reports-visual">

                <div class="platform-report-window">

                    <div class="platform-report-window-top">

                        <div>
                            <span>REPORTES</span>
                            <strong>Rendimiento mensual</strong>
                        </div>

                        <small>2026</small>

                    </div>


                    <div class="platform-report-chart">

                        <i style="height: 30%"></i>
                        <i style="height: 46%"></i>
                        <i style="height: 39%"></i>
                        <i style="height: 59%"></i>
                        <i style="height: 68%"></i>
                        <i style="height: 62%"></i>
                        <i style="height: 81%"></i>
                        <i style="height: 94%"></i>

                    </div>


                    <div class="platform-report-footer">

                        <span>Ventas</span>
                        <span>Pedidos</span>
                        <span>Operación</span>

                    </div>

                </div>

            </div>


            <div class="platform-reports-copy">

                <span class="platform-section-label">
                    04 · REPORTES
                </span>

                <h2>
                    Convierte tus datos
                    <strong>en decisiones.</strong>
                </h2>

                <p>
                    Los reportes permitirán consultar indicadores,
                    analizar tendencias y visualizar el comportamiento
                    de la operación empresarial.
                </p>


                <div class="platform-report-features">

                    <div>
                        <strong>Ventas</strong>
                        <span>Comportamiento comercial</span>
                    </div>

                    <div>
                        <strong>Pedidos</strong>
                        <span>Seguimiento de la operación</span>
                    </div>

                    <div>
                        <strong>Rendimiento</strong>
                        <span>Indicadores empresariales</span>
                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
         CTA
    ====================================================== --}}
    <section class="platform-final-cta">

        <div class="platform-container platform-final-cta-inner">

            <div>

                <span class="platform-section-label">
                    COMVERZA
                </span>

                <h2>
                    Una plataforma pensada
                    <strong>para crecer contigo.</strong>
                </h2>

            </div>


            <a href="{{ route('contacto') }}"
               class="platform-btn platform-btn-primary">

                Solicitar información

                <span>→</span>

            </a>

        </div>

    </section>

</div>

@endsection