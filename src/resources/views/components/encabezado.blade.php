@props(['eyebrow' => null, 'titulo', 'subtitulo' => null])
<div class="page-head">
    <div>
        @if ($eyebrow)<span class="eyebrow">{{ $eyebrow }}</span>@endif
        <h1>{{ $titulo }}</h1>
        @if ($subtitulo)<p>{{ $subtitulo }}</p>@endif
    </div>
    @if (trim($slot))
        <div class="page-actions">{{ $slot }}</div>
    @endif
</div>
