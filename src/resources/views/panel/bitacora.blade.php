@extends('layouts.panel')
@section('title', 'Bitácora')

@section('content')
<x-encabezado eyebrow="Seguridad" titulo="Bitácora de actividad" subtitulo="Registro de inicios de sesión, intentos fallidos, cambios y exportaciones." />

<div class="card">
    <form class="toolbar js-filtros" method="GET">
        <select name="modulo" style="max-width:220px"><option value="">Todos los módulos</option>@foreach ($modulos as $m)<option @selected(request('modulo') === $m)>{{ $m }}</option>@endforeach</select>
        <select name="accion" style="max-width:220px"><option value="">Todas las acciones</option>@foreach ($acciones as $a)<option @selected(request('accion') === $a)>{{ $a }}</option>@endforeach</select>
    </form>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Módulo</th><th>Descripción</th><th>IP</th></tr></thead>
            <tbody>
            @forelse ($registros as $r)
                @php $color = ['eliminar' => 'badge-red', 'crear' => 'badge-green', 'login_fallido' => 'badge-red', 'login' => 'badge-blue', 'exportar' => 'badge-purple'][$r->accion] ?? 'badge-gray'; @endphp
                <tr>
                    <td style="white-space:nowrap">{{ $r->created_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $r->usuario?->name ?? 'Anónimo' }}</td>
                    <td><span class="badge {{ $color }}">{{ $r->accion }}</span></td>
                    <td>{{ $r->modulo }}</td>
                    <td>{{ $r->descripcion }}</td>
                    <td style="color:var(--muted)">{{ $r->ip }}</td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty">Sin registros.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $registros->links() }}
</div>
@endsection
