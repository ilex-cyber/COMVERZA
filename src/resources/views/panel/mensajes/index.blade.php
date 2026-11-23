@extends('layouts.panel')
@section('title', 'Mensajes')

@section('content')
<x-encabezado eyebrow="Sitio web" titulo="Mensajes de contacto" subtitulo="Solicitudes recibidas desde el formulario público. Cada mensaje también llega por correo." />

<div class="card">
    <form class="toolbar js-filtros" method="GET">
        <div class="search"><i data-lucide="search"></i><input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o correo…"></div>
        <select name="tipo" style="max-width:180px">
            <option value="">Todos</option>
            @foreach (['Empresa', 'Proveedor', 'Otro'] as $t)<option @selected(request('tipo') === $t)>{{ $t }}</option>@endforeach
        </select>
        <button class="btn btn-outline">Filtrar</button>
    </form>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Remitente</th><th>Tipo</th><th>Mensaje</th><th>Recibido</th><th></th></tr></thead>
            <tbody>
            @forelse ($mensajes as $m)
                <tr style="{{ $m->leido ? '' : 'font-weight:700' }}">
                    <td>@unless($m->leido)<span class="dot" style="background:var(--blue)"></span>@endunless{{ $m->nombre }}<br><small style="color:var(--muted);font-weight:400">{{ $m->email }}</small></td>
                    <td><span class="chip">{{ $m->tipo }}</span></td>
                    <td style="max-width:380px;color:var(--muted);font-weight:400">{{ \Illuminate\Support\Str::limit($m->mensaje, 90) }} @if($m->adjunto)📎@endif</td>
                    <td style="font-weight:400">{{ $m->created_at->diffForHumans() }}</td>
                    <td><div class="actions">
                        <a class="btn btn-outline btn-icon" href="{{ route('panel.mensajes.show', $m) }}" title="Leer"><i data-lucide="eye"></i></a>
                        @can('mensajes.eliminar')<x-eliminar :action="route('panel.mensajes.destroy', $m)" :nombre="'mensaje de '.$m->nombre" />@endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty"><i data-lucide="inbox"></i>No hay mensajes.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $mensajes->links() }}
</div>
@endsection
