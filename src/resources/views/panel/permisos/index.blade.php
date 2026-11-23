@extends('layouts.panel')
@section('title', 'Permisos')

@section('content')
<x-encabezado eyebrow="Seguridad · RBAC" titulo="Permisos" subtitulo="Formato modulo.accion. Los permisos del sistema están protegidos; puedes crear permisos personalizados y asignarlos a roles.">
    @can('permisos.crear')<a class="btn btn-yellow" href="{{ route('panel.permisos.create') }}"><i data-lucide="plus"></i> Nuevo permiso</a>@endcan
</x-encabezado>

<div class="card">
    <form class="toolbar" method="GET">
        <div class="search"><i data-lucide="search"></i><input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar permiso…"></div>
        <button class="btn btn-outline">Buscar</button>
    </form>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Permiso</th><th>Tipo</th><th>Roles que lo tienen</th><th></th></tr></thead>
            <tbody>
            @foreach ($permisos as $p)
                @php $sistema = \App\Http\Controllers\Panel\PermisoController::esDelSistema($p); @endphp
                <tr>
                    <td><code style="font-weight:700;color:var(--navy)">{{ $p->name }}</code></td>
                    <td><span class="badge {{ $sistema ? 'badge-blue' : 'badge-yellow' }}">{{ $sistema ? 'Sistema' : 'Personalizado' }}</span></td>
                    <td>@forelse ($p->roles as $r)<span class="chip">{{ $r->name }}</span>@empty<span style="color:var(--muted)">Ninguno</span>@endforelse</td>
                    <td><div class="actions">
                        @unless ($sistema)
                            @can('permisos.editar')<a class="btn btn-outline btn-icon" href="{{ route('panel.permisos.edit', $p) }}"><i data-lucide="pencil"></i></a>@endcan
                            @can('permisos.eliminar')<x-eliminar :action="route('panel.permisos.destroy', $p)" :nombre="$p->name" />@endcan
                        @endunless
                    </div></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $permisos->links() }}
</div>
@endsection
