<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · COMVERZA</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=3">
    @stack('styles')
</head>
<body class="panel">
@php
    $menu = [
        'General' => [
            ['panel.dashboard', 'Dashboard', 'layout-dashboard', 'dashboard.ver', 'panel.dashboard'],
            ['panel.graficas', 'Gráficas', 'chart-column', 'dashboard.ver', 'panel.graficas'],
        ],
        'Operación' => [
            ['panel.pedidos.index', 'Pedidos', 'shopping-cart', 'pedidos.ver', 'panel.pedidos.*'],
            ['panel.productos.index', 'Productos', 'package', 'productos.ver', 'panel.productos.*'],
            ['panel.categorias.index', 'Categorías', 'tags', 'categorias.ver', 'panel.categorias.*'],
            ['panel.clientes.index', 'Clientes', 'building-2', 'clientes.ver', 'panel.clientes.*'],
            ['panel.proveedores.index', 'Proveedores', 'truck', 'proveedores.ver', 'panel.proveedores.*'],
        ],
        'Sitio web' => [
            ['panel.banners.index', 'Fotos de la landing', 'images', 'banners.ver', 'panel.banners.*'],
            ['panel.mensajes.index', 'Mensajes', 'mail', 'mensajes.ver', 'panel.mensajes.*'],
            ['panel.consola-api', 'Consola API', 'braces', 'dashboard.ver', 'panel.consola-api'],
        ],
        'Seguridad' => [
            ['panel.usuarios.index', 'Usuarios', 'users', 'usuarios.ver', 'panel.usuarios.*'],
            ['panel.roles.index', 'Roles', 'shield-check', 'roles.ver', 'panel.roles.*'],
            ['panel.permisos.index', 'Permisos', 'key-round', 'permisos.ver', 'panel.permisos.*'],
            ['panel.bitacora', 'Bitácora', 'scroll-text', 'bitacora.ver', 'panel.bitacora'],
        ],
    ];
    $sinLeer = auth()->user()->can('mensajes.ver') ? \App\Models\MensajeContacto::where('leido', false)->count() : 0;
@endphp

<div class="panel-shell">
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('home') }}" class="sidebar-logo"><img src="{{ asset('images/logo_comverza.png') }}" alt="COMVERZA"></a>

        @foreach ($menu as $seccion => $items)
            @php $visibles = collect($items)->filter(fn ($i) => auth()->user()->can($i[3])); @endphp
            @if ($visibles->isNotEmpty())
                <div>
                    <div class="sidebar-section">{{ $seccion }}</div>
                    <nav>
                        @foreach ($visibles as [$ruta, $texto, $icono, $permiso, $activo])
                            <a href="{{ route($ruta) }}" class="{{ request()->routeIs($activo) ? 'active' : '' }}">
                                <i data-lucide="{{ $icono }}"></i> {{ $texto }}
                                @if ($ruta === 'panel.mensajes.index' && $sinLeer)<span class="count">{{ $sinLeer }}</span>@endif
                            </a>
                        @endforeach
                    </nav>
                </div>
            @endif
        @endforeach

        <div class="sidebar-foot">
            <strong>{{ auth()->user()->getRoleNames()->implode(', ') ?: 'Sin rol' }}</strong>
            Tu menú muestra solo los módulos que tu rol tiene permitidos.
        </div>
    </aside>

    <div class="panel-main">
        <header class="topbar">
            <button class="menu-btn" type="button" data-toggle-sidebar aria-label="Abrir menú"><i data-lucide="menu"></i></button>
            <span class="topbar-title">@yield('title', 'Panel')</span>
            <div class="topbar-right">
                <a href="{{ route('home') }}" class="site-link" target="_blank">Ver sitio público ↗</a>
                <div class="user-chip" data-user-menu>
                    <img src="{{ auth()->user()->avatar_url }}" alt="">
                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->puesto ?? auth()->user()->email }}</small>
                    </div>
                    <div class="user-menu">
                        <a href="{{ route('panel.perfil') }}">Mi perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            @if ($errors->any())
                <div class="alert alert-error">
                    <i data-lucide="circle-alert"></i>
                    <div>
                        Revisa la información capturada:
                        <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Librerías de interfaz: íconos (Lucide), alertas (SweetAlert2) y gráficas (Chart.js) --}}
<script src="https://cdn.jsdelivr.net/npm/lucide@0.468.0/dist/umd/lucide.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    window.COMVERZA_FLASH = {
        success: @json(session('success')),
        error: @json(session('error')),
    };
</script>
<script src="{{ asset('js/panel.js') }}?v=3"></script>
@stack('scripts')
</body>
</html>
