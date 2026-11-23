@extends('layouts.panel')
@section('title', 'Mensaje')

@section('content')
<x-encabezado eyebrow="Mensaje de contacto" :titulo="$mensaje->nombre" :subtitulo="$mensaje->email.' · '.$mensaje->created_at->format('d/m/Y H:i')">
    <a class="btn btn-outline" href="{{ route('panel.mensajes.index') }}">← Volver</a>
    <a class="btn btn-yellow" href="mailto:{{ $mensaje->email }}?subject=Re: tu mensaje a COMVERZA"><i data-lucide="reply"></i> Responder</a>
</x-encabezado>
<div class="card card-pad" style="max-width:820px">
    <dl class="detail-list">
        <dt>Empresa</dt><dd>{{ $mensaje->empresa ?: '—' }}</dd>
        <dt>Tipo</dt><dd><span class="chip">{{ $mensaje->tipo }}</span></dd>
        <dt>Adjunto</dt><dd>@if ($mensaje->adjunto)<a class="link" target="_blank" href="{{ asset($mensaje->adjunto) }}">Abrir archivo ↗</a>@else — @endif</dd>
    </dl>
    <p style="margin-top:20px;line-height:1.7;white-space:pre-line">{{ $mensaje->mensaje }}</p>
</div>
@endsection
