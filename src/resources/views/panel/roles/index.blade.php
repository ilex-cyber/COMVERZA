@extends('layouts.panel')
@section('title', 'Roles')

@section('content')
<x-encabezado eyebrow="Seguridad · RBAC" titulo="Roles" subtitulo="Un rol agrupa permisos. El rol Administrador es del sistema y siempre tiene acceso total.">
    @can('roles.crear')<a class="btn btn-yellow" href="{{ route('panel.roles.create') }}"><i data-lucide="plus"></i> Nuevo rol</a>@endcan
</x-encabezado>

<div class="grid grid-4">
    @foreach ($roles as $r)
        <div class="card card-pad">
            <div class="kpi-icon {{ ['', 'yellow', 'purple', 'green'][$loop->index % 4] }}" style="margin-bottom:14px"><i data-lucide="shield-check"></i></div>
            <strong style="font-size:17px;color:var(--navy)">{{ $r->name }}</strong>
            @if (in_array($r->name, \App\Http\Controllers\Panel\RolController::PROTEGIDOS))<span class="chip">Sistema</span>@endif
            <p style="color:var(--muted);margin:6px 0 16px">{{ $r->permissions_count }} permisos · {{ $r->users_count }} usuario(s)</p>
            <div class="actions" style="justify-content:flex-start">
                @can('roles.editar')<a class="btn btn-outline btn-sm" href="{{ route('panel.roles.edit', $r) }}"><i data-lucide="pencil"></i> Permisos</a>@endcan
                @can('roles.eliminar')@unless (in_array($r->name, \App\Http\Controllers\Panel\RolController::PROTEGIDOS))
                    <x-eliminar :action="route('panel.roles.destroy', $r)" :nombre="$r->name" :mensaje="$r->users_count ? 'Tiene '.$r->users_count.' usuario(s): el sistema bloqueará la eliminación.' : null" />
                @endunless @endcan
            </div>
        </div>
    @endforeach
</div>
@endsection
