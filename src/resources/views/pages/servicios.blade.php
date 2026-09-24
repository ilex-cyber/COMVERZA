@extends('layouts.app')

@section('title', 'Servicios | COMVERZA')


@section('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/services.css') }}"
>

@endsection


@section('content')

<div class="services-page">


    <!-- ==================================
         HERO
    =================================== -->

    <section class="services-hero">

        <div class="container services-hero-grid">


            <div class="services-copy reveal">

                <span class="services-label">
                    SOLUCIONES COMVERZA
                </span>


                <h1>
                    Tecnología para
                    <span>
                        transformar tu operación.
                    </span>
                </h1>


                <p>
                    Diseñamos soluciones digitales que ayudan
                    a las empresas a conectar procesos, organizar
                    información y mejorar la gestión de su operación.
                </p>


                <div class="hero-buttons">

                    <a
                        href="#soluciones"
                        class="btn btn-yellow"
                    >
                        Conocer soluciones
                        <span>→</span>
                    </a>


                    <a
                        href="{{ route('contacto') }}"
                        class="btn btn-outline"
                    >
                        Hablar con nosotros
                    </a>

                </div>

            </div>


            <!-- VISUAL -->

            <div class="services-visual reveal">


                <div class="services-orbit"></div>


                <div class="services-panel">


                    <div class="services-panel-top">

                        <div class="services-panel-brand">

                            <span>
                                C
                            </span>

                            COMVERZA

                        </div>


                        <div class="services-panel-status">

                            ● Plataforma activa

                        </div>

                    </div>


                    <div class="service-mini-grid">


                        <div class="service-mini">

                            <div class="service-mini-icon">
                                ◈
                            </div>

                            <strong>
                                Gestión empresarial
                            </strong>

                            <span>
                                Organización y control
                                de tus procesos.
                            </span>

                        </div>


                        <div class="service-mini">

                            <div class="service-mini-icon">
                                ◇
                            </div>

                            <strong>
                                Procesos conectados
                            </strong>

                            <span>
                                Información centralizada
                                en un mismo entorno.
                            </span>

                        </div>


                        <div class="service-mini">

                            <div class="service-mini-icon">
                                ◎
                            </div>

                            <strong>
                                Información
                            </strong>

                            <span>
                                Mayor visibilidad para
                                tomar decisiones.
                            </span>

                        </div>


                        <div class="service-mini">

                            <div class="service-mini-icon">
                                ✦
                            </div>

                            <strong>
                                Evolución digital
                            </strong>

                            <span>
                                Soluciones preparadas para
                                crecer contigo.
                            </span>

                        </div>


                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- ==================================
         SOLUCIONES
    =================================== -->

    <section
        class="services-detail"
        id="soluciones"
    >

        <div class="container">


            <div class="services-detail-heading reveal">

                <span class="services-label">
                    ¿QUÉ OFRECEMOS?
                </span>


                <h2>
                    Soluciones pensadas para
                    <span>
                        tu negocio.
                    </span>
                </h2>


                <p>
                    COMVERZA reúne herramientas y servicios
                    orientados a mejorar diferentes áreas de
                    la operación empresarial.
                </p>

            </div>


            <div class="services-grid">


                <article class="service-feature reveal">

                    <div class="service-feature-icon">
                        ◈
                    </div>

                    <h3>
                        Gestión digital
                    </h3>

                    <p>
                        Centraliza la información de tu negocio
                        y facilita el seguimiento de tus procesos.
                    </p>

                </article>


                <article class="service-feature reveal">

                    <div class="service-feature-icon">
                        ◎
                    </div>

                    <h3>
                        Integración
                    </h3>

                    <p>
                        Conecta diferentes áreas y relaciones
                        comerciales dentro de un mismo entorno.
                    </p>

                </article>


                <article class="service-feature reveal">

                    <div class="service-feature-icon">
                        ✦
                    </div>

                    <h3>
                        Automatización
                    </h3>

                    <p>
                        Reduce tareas repetitivas y mejora
                        la eficiencia de tus operaciones.
                    </p>

                </article>

            </div>

        </div>

    </section>



    <!-- ==================================
         PROCESO
    =================================== -->

    <section class="services-process">

        <div class="container">


            <div class="services-detail-heading reveal">

                <span class="services-label">
                    NUESTRO ENFOQUE
                </span>


                <h2>
                    Del proceso a la
                    <span>
                        solución.
                    </span>
                </h2>


                <p>
                    Una metodología enfocada en entender,
                    conectar y mejorar cada parte de la operación.
                </p>

            </div>


            <div class="process-grid">


                <article class="process-step reveal">

                    <span class="process-number">
                        01
                    </span>

                    <h3>
                        Analizamos
                    </h3>

                    <p>
                        Identificamos las necesidades
                        y procesos de la empresa.
                    </p>

                </article>


                <article class="process-step reveal">

                    <span class="process-number">
                        02
                    </span>

                    <h3>
                        Diseñamos
                    </h3>

                    <p>
                        Definimos soluciones orientadas
                        a los objetivos del negocio.
                    </p>

                </article>


                <article class="process-step reveal">

                    <span class="process-number">
                        03
                    </span>

                    <h3>
                        Conectamos
                    </h3>

                    <p>
                        Integramos procesos e información
                        dentro de un entorno centralizado.
                    </p>

                </article>


                <article class="process-step reveal">

                    <span class="process-number">
                        04
                    </span>

                    <h3>
                        Evolucionamos
                    </h3>

                    <p>
                        Mejoramos continuamente la solución
                        conforme crece la operación.
                    </p>

                </article>


            </div>

        </div>

    </section>



    <!-- ==================================
         CTA
    =================================== -->

    <section class="services-cta">

        <div class="container">

            <div class="services-cta-card reveal">

                <div>

                    <h2>
                        Hagamos crecer
                        tu operación.
                    </h2>

                    <p>
                        Conoce cómo COMVERZA puede ayudarte
                        a construir una gestión empresarial
                        más conectada.
                    </p>

                </div>


                <a
                    href="{{ route('contacto') }}"
                    class="btn btn-yellow"
                >
                    Comenzar ahora
                    <span>→</span>
                </a>

            </div>

        </div>

    </section>


</div>

@endsection