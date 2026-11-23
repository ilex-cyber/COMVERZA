@extends('layouts.app')

@section('title', 'Catálogo | COMVERZA')

@section('content')
<section class="page-hero" style="background-image:linear-gradient(100deg, rgba(6,17,47,.97) 40%, rgba(6,17,47,.6)), url('{{ asset('images/landing/centro-distribucion.jpg') }}')">
    <div class="container">
        <span class="hero-badge"><span></span>Catálogo empresarial</span>
        <h1>Encuentra lo que tu empresa <strong>necesita hoy.</strong></h1>
        <p>Precios y existencias en tiempo real, directo de nuestro inventario.</p>
        <form class="catalog-search" id="formBuscar" onsubmit="return false">
            <input type="search" id="buscar" placeholder="Buscar por nombre o SKU…" maxlength="60" aria-label="Buscar productos">
            <button class="btn btn-yellow" type="submit">Buscar</button>
        </form>
    </div>
</section>

<section class="api-section api-section--light" style="padding-top:40px">
    <div class="container">
        <div class="catalog-filters" id="filtros">
            <button class="chip-filter active" data-cat="">Todas</button>
        </div>
        <p class="catalog-count" id="conteo">Cargando productos…</p>
        <div class="product-grid" id="catalogo">
            @for ($i = 0; $i < 8; $i++)<div class="skeleton skeleton-product"></div>@endfor
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container cta-box" style="background-image:linear-gradient(110deg, rgba(6,17,47,.96) 35%, rgba(6,17,47,.55)), url('{{ asset('images/landing/alianza-comercial.jpg') }}')">
        <div>
            <span class="section-label">COMPRAS PARA EMPRESAS</span>
            <h2>¿Necesitas una cotización por volumen? <span>Hablemos.</span></h2>
        </div>
        <div class="cta-actions"><a href="{{ route('contacto') }}" class="btn btn-yellow">Solicitar cotización →</a></div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    window.COMVERZA_API = '{{ url('/api/v1') }}';
    window.COMVERZA_CATALOGO = '{{ route('catalogo') }}';
</script>
<script src="{{ asset('js/landing-api.js') }}?v=3"></script>
<script>
// Catálogo: consume GET /api/v1/productos (con filtros) y GET /api/v1/categorias
document.addEventListener('DOMContentLoaded', () => {
    const API = window.COMVERZA_API, $ = id => document.getElementById(id);
    const params = new URLSearchParams(location.search);
    let categoria = params.get('categoria') || '', timer;

    fetch(`${API}/categorias?activas=1`).then(r => r.json()).then(({ data }) => {
        $('filtros').insertAdjacentHTML('beforeend', data.filter(c => c.productos_count).map(c =>
            `<button class="chip-filter" data-cat="${c.id}" style="--cat:${c.color}">${c.nombre} <small>${c.productos_count}</small></button>`).join(''));
        marcar();
    });

    function marcar() { document.querySelectorAll('.chip-filter').forEach(b => b.classList.toggle('active', b.dataset.cat === String(categoria))); }

    function cargar() {
        const q = new URLSearchParams({ activos: 1 });
        if (categoria) q.set('categoria', categoria);
        if ($('buscar').value.trim()) q.set('buscar', $('buscar').value.trim());
        $('conteo').textContent = 'Cargando productos…';
        fetch(`${API}/productos?${q}`).then(r => r.json()).then(({ data, total }) => {
            $('conteo').textContent = `${total} producto${total === 1 ? '' : 's'} encontrado${total === 1 ? '' : 's'}`;
            $('catalogo').innerHTML = data.length ? data.map(window.COMVERZA_tarjetaProducto).join('')
                : '<div class="api-error" style="grid-column:1/-1">No encontramos productos con esos filtros.</div>';
            const resaltado = params.get('producto');
            if (resaltado) document.querySelectorAll('.product-card').forEach((el, i) => { if (data[i] && String(data[i].id) === resaltado) { el.classList.add('highlight'); el.scrollIntoView({ block: 'center' }); } });
        }).catch(() => $('catalogo').innerHTML = '<div class="api-error">No pudimos cargar el catálogo.</div>');
    }

    $('filtros').addEventListener('click', e => {
        const b = e.target.closest('.chip-filter'); if (!b) return;
        categoria = b.dataset.cat; marcar(); cargar();
        history.replaceState(null, '', categoria ? `?categoria=${categoria}` : location.pathname);
    });
    $('buscar').addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(cargar, 350); });
    $('formBuscar').addEventListener('submit', cargar);
    cargar();
});
</script>
@endpush
