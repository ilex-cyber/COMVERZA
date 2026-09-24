@extends('layouts.app')

@section('title', 'Contacto | COMVERZA')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endsection

@section('content')

<div class="contact-page">

    {{-- =====================================================
         HERO
    ====================================================== --}}
    <section class="contact-hero">

        <div class="contact-container contact-hero-grid">

            <div class="contact-hero-copy">

                <span class="contact-eyebrow">
                    <i></i>
                    HABLEMOS
                </span>

                <h1>
                    Conectemos para
                    <strong>dar forma a tu proyecto.</strong>
                </h1>

                <p>
                    Cuéntanos qué necesitas, qué proceso quieres mejorar
                    o cómo te gustaría integrar COMVERZA dentro de tu
                    operación empresarial.
                </p>

                <div class="contact-hero-tags">

                    <span>Empresas</span>
                    <span>Proveedores</span>
                    <span>Soluciones digitales</span>

                </div>

            </div>


            <div class="contact-hero-card">

                <span>COMVERZA</span>

                <strong>
                    Una plataforma construida
                    para conectar negocios.
                </strong>

                <p>
                    Estamos desarrollando una experiencia empresarial
                    centralizada para facilitar la gestión y comunicación
                    entre diferentes participantes.
                </p>

                <div class="contact-hero-line"></div>

                <small>
                    Atención empresarial
                </small>

            </div>

        </div>

    </section>


    {{-- =====================================================
         CONTACTO + FORMULARIO
    ====================================================== --}}
    <section class="contact-main">

        <div class="contact-container contact-grid">

            <div class="contact-information">

                <span class="contact-section-label">
                    CONTACTO
                </span>

                <h2>
                    Ponte en contacto
                    <strong>con COMVERZA.</strong>
                </h2>

                <p>
                    Utiliza el formulario para enviarnos tus dudas,
                    comentarios o información relacionada con el proyecto.
                </p>


                <div class="contact-info-list">

                    <div class="contact-info-item">

                        <div class="contact-info-icon">
                            ✉
                        </div>

                        <div>
                            <span>Correo electrónico</span>
                            <strong>contacto@comverza.com</strong>
                        </div>

                    </div>


                    <div class="contact-info-item">

                        <div class="contact-info-icon">
                            ☎
                        </div>

                        <div>
                            <span>Teléfono</span>
                            <strong>Próximamente disponible</strong>
                        </div>

                    </div>


                    <div class="contact-info-item">

                        <div class="contact-info-icon">
                            ◉
                        </div>

                        <div>
                            <span>Ubicación</span>
                            <strong>México</strong>
                        </div>

                    </div>

                </div>


                <div class="contact-note">

                    <span>i</span>

                    <p>
                        Los mensajes enviados desde este formulario
                        se procesan mediante Laravel.
                    </p>

                </div>

            </div>


            {{-- FORMULARIO --}}
            <div class="contact-form-wrapper">

                @if(session('success'))

                    <div class="contact-success">
                        ✓ {{ session('success') }}
                    </div>

                @endif


                @if($errors->any())

                    <div class="contact-errors">

                        <strong>
                            Revisa la información:
                        </strong>

                        <ul>

                            @foreach($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                <form
                    action="{{ route('contacto.enviar') }}"
                    method="POST"
                    class="contact-form"
                >

                    @csrf


                    <div class="contact-form-heading">

                        <span>ENVÍA UN MENSAJE</span>

                        <h3>
                            Cuéntanos sobre tu necesidad.
                        </h3>

                    </div>


                    <div class="contact-form-row">

                        <div class="contact-field">

                            <label for="nombre">
                                Nombre
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                placeholder="Tu nombre"
                                required
                            >

                        </div>


                        <div class="contact-field">

                            <label for="empresa">
                                Empresa
                            </label>

                            <input
                                type="text"
                                id="empresa"
                                name="empresa"
                                value="{{ old('empresa') }}"
                                placeholder="Nombre de tu empresa"
                            >

                        </div>

                    </div>


                    <div class="contact-form-row">

                        <div class="contact-field">

                            <label for="email">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="nombre@empresa.com"
                                required
                            >

                        </div>


                        <div class="contact-field">

                            <label for="tipo">
                                Tipo de consulta
                            </label>

                            <select
                                id="tipo"
                                name="tipo"
                                required
                            >

                                <option value="">
                                    Selecciona una opción
                                </option>

                                <option
                                    value="Empresa"
                                    {{ old('tipo') === 'Empresa' ? 'selected' : '' }}
                                >
                                    Empresa
                                </option>

                                <option
                                    value="Proveedor"
                                    {{ old('tipo') === 'Proveedor' ? 'selected' : '' }}
                                >
                                    Proveedor
                                </option>

                                <option
                                    value="Otro"
                                    {{ old('tipo') === 'Otro' ? 'selected' : '' }}
                                >
                                    Otro
                                </option>

                            </select>

                        </div>

                    </div>


                    <div class="contact-field">

                        <label for="mensaje">
                            Mensaje
                        </label>

                        <textarea
                            id="mensaje"
                            name="mensaje"
                            rows="6"
                            placeholder="Escribe tu mensaje..."
                            required
                        >{{ old('mensaje') }}</textarea>

                    </div>


                    <button
                        type="submit"
                        class="contact-submit"
                    >

                        Enviar mensaje

                        <span>→</span>

                    </button>


                    <p class="contact-form-footnote">
                        Al enviar este formulario, Laravel validará
                        la información antes de procesarla.
                    </p>

                </form>

            </div>

        </div>

    </section>


    {{-- =====================================================
         CTA FINAL
    ====================================================== --}}
    <section class="contact-final">

        <div class="contact-container contact-final-inner">

            <div>

                <span class="contact-section-label">
                    COMVERZA
                </span>

                <h2>
                    Una conversación puede ser
                    <strong>el comienzo.</strong>
                </h2>

            </div>

            <a
                href="{{ route('home') }}"
                class="contact-final-button"
            >
                Volver a Inicio
                <span>→</span>
            </a>

        </div>

    </section>

</div>

@endsection