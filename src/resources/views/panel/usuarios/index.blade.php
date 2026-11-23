@extends('layouts.panel')
@section('title', 'Usuarios')

@section('content')
<x-encabezado eyebrow="Seguridad · RBAC" titulo="Usuarios" subtitulo="Cada usuario obtiene sus permisos a través de los roles asignados.">
    @can('usuarios.crear')<a class="btn btn-yellow" href="{{ route('panel.usuarios.create') }}"><i data-lucide="user-plus"></i> Nuevo usuario</a>@endcan
</x-encabezado>

<div class="card">
    <form class="toolbar js-filtros" method="GET">
        <div class="search"><i data-lucide="search"></i><input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o correo…"></div>
        <select name="rol" style="max-width:200px">
            <option value="">Todos los roles</option>
            @foreach ($roles as $r)<option @selected(request('rol') === $r)>{{ $r }}</option>@endforeach
        </select>
        <button class="btn btn-outline">Filtrar</button>
    </form>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Usuario</th><th>Roles</th><th>Último acceso</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @foreach ($usuarios as $u)
                <tr>
                    <td><div class="cell-main"><img src="{{ $u->avatar_url }}" style="border-radius:50%" alt=""><div><strong>{{ $u->name }} @if($u->is(auth()->user()))<span class="chip">Tú</span>@endif</strong><small>{{ $u->email }} · {{ $u->puesto }}</small></div></div></td>
                    <td>@foreach ($u->roles as $r)<span class="badge badge-purple">{{ $r->name }}</span> @endforeach</td>
                    <td style="color:var(--muted)">{{ $u->ultimo_acceso?->diffForHumans() ?? 'Nunca' }}</td>
                    <td><span class="badge {{ $u->activo ? 'badge-green' : 'badge-red' }}">{{ $u->activo ? 'Activo' : 'Desactivado' }}</span></td>
                    <td><div class="actions">
                        @can('usuarios.editar')<a class="btn btn-outline btn-icon" href="{{ route('panel.usuarios.edit', $u) }}" title="Editar"><i data-lucide="pencil"></i></a>@endcan
                        @can('usuarios.eliminar')@unless($u->is(auth()->user()))<x-eliminar :action="route('panel.usuarios.destroy', $u)" :nombre="$u->name" />@endunless @endcan
                    </div></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $usuarios->links() }}
</div>
@endsection
