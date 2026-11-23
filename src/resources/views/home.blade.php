@extends('layouts.app')

@section('title', 'COMVERZA | Tecnología que impulsa tu negocio')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.1.15/swiper-bundle.min.css">
@endsection

@section('content')

{{-- ========================= HERO ========================= --}}
<section class="hero">
    <div class="hero-glow glow-one"></div>
    <div class="hero-glow glow-two"></div>
    <div class="hero-grid"></div>

    <div class="container hero-content">
        <div class="hero-text reveal">
            <span class="hero-badge"><span></span>Plataforma empresarial</span>
            <h1>Conecta, centraliza y <strong>gestiona tu negocio</strong> desde un solo lugar.</h1>
            <p>COMVERZA es una plataforma empresarial diseñada para conectar empresas, clientes y proveedores en un entorno centralizado, seguro y eficiente.</p>
            <div class="hero-buttons">
                <a href="{{ route('catalogo') }}" class="btn btn-yellow">Explorar catálogo <span>→</span></a>
                <a href="#nosotros" class="btn btn-outline">Conocer COMVERZA</a>
            </div>
        </div>
        <div class="hero-visual reveal">
            @include('components.comverza-dashboard')
        </div>
    </div>
</section>

{{-- ========================= INDICADORES (API) ========================= --}}
<section class="stats-section">
    <div class="container stats-grid">
        <article class="stat-item reveal">
            <div class="stat-icon">◈</div>
            <div><strong data-api-count="productos">—</strong><span>Productos en catálogo</span></div>
        </article>
        <article class="stat-item reveal">
            <div class="stat-icon">▦</div>
            <div><strong data-api-count="categorias">—</strong><span>Categorías de negocio</span></div>
        </article>
        <article class="stat-item reveal">
            <div class="stat-icon">👥</div>
            <div><strong data-api-count="proveedores">—</strong><span>Proveedores verificados</span></div>
        </article>
        <article class="stat-item reveal">
            <div class="stat-icon">◉</div>
            <div><strong>24/7</strong><span>Soporte y acompañamiento</span></div>
        </article>
    </div>
</section>

{{-- ========================= CARRUSEL DE FOTOS ========================= --}}
@if ($banners->count())
<section class="showcase-section">
    <div class="container">
        <div class="landing-heading reveal">
            <span class="section-label">COMVERZA EN ACCIÓN</span>
            <h2>Una plataforma pensada para <span>operaciones reales.</span></h2>
        </div>
        <div class="swiper showcase-swiper reveal">
            <div class="swiper-wrapper">
                @foreach ($banners as $b)
                    <div class="swiper-slide">
                        <div class="showcase-slide" style="background-image:url('{{ $b->imagen_url }}')">
                            <div class="showcase-caption">
                                <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($banners->count(), 2, '0', STR_PAD_LEFT) }}</span>
                                <h3>{{ $b->titulo }}</h3>
                                @if ($b->subtitulo)<p>{{ $b->subtitulo }}</p>@endif
                                @if ($b->enlace)<a class="btn btn-yellow" href="{{ $b->enlace }}">Conocer más →</a>@endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</section>
@endif

{{-- ========================= ¿QUÉ ES COMVERZA? ========================= --}}
<section class="about-section" id="nosotros">
    <div class="container about-grid">
        <div class="about-image reveal">
            <img src="{{ asset('images/que_somos_comverza.png') }}" alt="COMVERZA - Plataforma empresarial">
        </div>
        <div class="about-content reveal">
            <span class="section-label">¿QUÉ ES COMVERZA?</span>
            <h2>Una visión para <span>centralizar la gestión empresarial.</span></h2>
            <p>COMVERZA es una plataforma web B2B que integra productos, clientes, proveedores, pedidos, facturación y logística en un mismo entorno digital.</p>
            <div class="benefits">
                <div class="benefit"><div class="benefit-icon">✓</div><div><strong>Más eficiencia</strong><span>Simplifica tus procesos.</span></div></div>
                <div class="benefit"><div class="benefit-icon">✓</div><div><strong>Mayor control</strong><span>Ten una visión más clara.</span></div></div>
                <div class="benefit"><div class="benefit-icon">✓</div><div><strong>Más conexión</strong><span>Mantén todo conectado.</span></div></div>
            </div>
        </div>
    </div>
</section>

{{-- ========================= GALERÍA ========================= --}}
<section class="gallery-section">
    <div class="container">
        <div class="landing-heading landing-heading--split reveal">
            <div>
                <span class="section-label">DE LA BODEGA A TU CLIENTE</span>
                <h2>Cada etapa de tu operación, <span>conectada.</span></h2>
            </div>
            <p>Desde el almacén y la logística hasta el análisis de datos y la relación con tus clientes, COMVERZA acompaña a todo tu equipo.</p>
        </div>
        <div class="gallery-grid">
            @foreach ([
                ['almacen-inventario.jpg', 'Inventario', 'Stock en tiempo real con alertas automáticas.', 'tall'],
                ['metricas-tiempo-real.jpg', 'Analítica', 'Dashboards de impacto para decidir mejor.', ''],
                ['presentacion-equipo.jpg', 'Equipos', 'Roles y permisos para cada colaborador.', ''],
                ['infraestructura-nube.jpg', 'Infraestructura', 'Seguridad y disponibilidad en la nube.', 'wide'],
                ['exito-clientes.jpg', 'Clientes', 'Pedidos y seguimiento sin fricción.', ''],
            ] as [$img, $titulo, $texto, $clase])
                <figure class="gallery-item {{ $clase }} reveal">
                    <img src="{{ asset('images/landing/'.$img) }}" alt="{{ $titulo }}" loading="lazy">
                    <figcaption><strong>{{ $titulo }}</strong><span>{{ $texto }}</span></figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>

{{-- ========================= CATEGORÍAS (API GET /categorias) ========================= --}}
<section class="api-section api-section--light" id="categorias">
    <div class="container">
        <div class="landing-heading landing-heading--split reveal">
            <div>
                <span class="section-label">EXPLORA POR CATEGORÍA</span>
                <h2>Todo lo que tu empresa <span>necesita para operar.</span></h2>
            </div>
            <a class="landing-link" href="{{ route('catalogo') }}">Ver catálogo completo →</a>
        </div>
        <div class="category-grid" id="apiCategorias">
            @for ($i = 0; $i < 8; $i++)<div class="skeleton skeleton-card"></div>@endfor
        </div>
    </div>
</section>

{{-- ========================= PRODUCTOS DESTACADOS (API GET /productos) ========================= --}}
<section class="api-section" id="productos">
    <div class="container">
        <div class="landing-heading landing-heading--split reveal">
            <div>
                <span class="section-label">PRODUCTOS DESTACADOS</span>
                <h2>Disponibles hoy, <span>con precio y existencias reales.</span></h2>
            </div>
            <a class="landing-link" href="{{ route('catalogo') }}">Ver todos los productos →</a>
        </div>
        <div class="product-grid" id="apiProductos">
            @for ($i = 0; $i < 8; $i++)<div class="skeleton skeleton-product"></div>@endfor
        </div>
    </div>
</section>

{{-- ========================= MÓDULOS ========================= --}}
<section class="modules-section" id="modulos">
    <div class="container">
        <div class="modules-heading reveal">
            <div>
                <span class="section-label">MÓDULOS DE LA PLATAFORMA</span>
                <h2>Todo lo que necesitas <span>en un solo lugar.</span></h2>
            </div>
            <p>Cada módulo está diseñado para simplificar tus procesos y ayudarte a mantener tu negocio conectado.</p>
        </div>
        <div class="modules-grid">
            @foreach ([['◈', 'Productos', 'Catálogo y control de inventario.', route('catalogo')], ['🛒', 'Pedidos', 'Seguimiento y gestión comercial.', route('plataforma')], ['👥', 'Proveedores', 'Red de proveedores y relaciones comerciales.', route('home').'#proveedores'], ['▤', 'Reportes', 'PDF, Excel y dashboards de impacto.', route('plataforma')]] as [$ico, $t, $d, $url])
                <article class="module-card reveal">
                    <div class="module-icon">{{ $ico }}</div>
                    <div class="module-content"><h3>{{ $t }}</h3><p>{{ $d }}</p></div>
                    <a href="{{ $url }}" aria-label="{{ $t }}">→</a>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ========================= PROVEEDORES (API GET /proveedores) ========================= --}}
<section class="api-section api-section--dark" id="proveedores">
    <div class="container">
        <div class="landing-heading landing-heading--split reveal">
            <div>
                <span class="section-label">RED DE PROVEEDORES</span>
                <h2>Aliados verificados <span>en todo México.</span></h2>
            </div>
            <p>Trabajamos con proveedores calificados para garantizar abasto, calidad y tiempos de entrega.</p>
        </div>
        <div class="provider-grid" id="apiProveedores">
            @for ($i = 0; $i < 6; $i++)<div class="skeleton skeleton-card dark"></div>@endfor
        </div>
    </div>
</section>

{{-- ========================= CTA ========================= --}}
<section class="cta-section">
    <div class="container cta-box reveal" style="background-image:linear-gradient(110deg, rgba(6,17,47,.96) 35%, rgba(6,17,47,.55)), url('{{ asset('images/landing/oficina-operaciones.jpg') }}')">
        <div>
            <span class="section-label">¿LISTO PARA CRECER?</span>
            <h2>Lleva tu operación al siguiente nivel con <span>COMVERZA.</span></h2>
            <p>Agenda una demostración y descubre cómo centralizar tu negocio en días, no en meses.</p>
        </div>
        <div class="cta-actions">
            <a href="{{ route('contacto') }}" class="btn btn-yellow">Solicitar demostración →</a>
            <a href="{{ route('login') }}" class="btn btn-outline">Acceso a clientes</a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11.1.15/swiper-bundle.min.js"></script>
<script>
    window.COMVERZA_API = '{{ url('/api/v1') }}';
    window.COMVERZA_CATALOGO = '{{ route('catalogo') }}';
</script>
<script src="{{ asset('js/landing-api.js') }}?v=3"></script>
@endpush
