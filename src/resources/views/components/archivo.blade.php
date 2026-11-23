@props([
    'name', 'label', 'accept' => 'image/jpeg,image/png,image/webp', 'maxMb' => 2, 'multiple' => false,
    'hint' => null, 'tipos' => 'JPG, PNG o WEBP', 'minWidth' => null, 'actual' => null, 'actualEsImagen' => true,
    'required' => false, 'full' => true, 'maxFiles' => null,
])
@php $key = str_replace('[]', '', $name); @endphp
<div class="field {{ $full ? 'full' : '' }}">
    <label>{{ $label }} @if ($required)<span class="req">*</span>@endif</label>

    @if ($actual)
        <div class="previews" style="margin:0 0 10px">
            <div class="preview">
                @if ($actualEsImagen)
                    <img src="{{ asset($actual) }}" alt="Archivo actual">
                @else
                    <a class="file-doc" href="{{ asset($actual) }}" target="_blank"><div><b>PDF</b>Ver actual</div></a>
                @endif
                <span class="size">Actual</span>
            </div>
        </div>
    @endif

    <div class="dropzone {{ $errors->has($key) || $errors->has($key.'.*') ? 'has-error' : '' }}">
        <input type="file" name="{{ $name }}" accept="{{ $accept }}" data-max-mb="{{ $maxMb }}"
               data-tipos-texto="{{ $tipos }}" @if($multiple) multiple @endif
               @if($minWidth) data-min-width="{{ $minWidth }}" @endif
               @if($maxFiles) data-max-files="{{ $maxFiles }}" @endif
               @required($required)>
        <i data-lucide="cloud-upload"></i>
        <strong>{{ $multiple ? 'Arrastra tus archivos aquí' : 'Arrastra tu archivo aquí' }} o haz clic</strong>
        <small>{{ $tipos }} · máximo {{ $maxMb }} MB{{ $multiple ? ' por archivo' : '' }}{{ $minWidth ? " · mínimo {$minWidth}px de ancho" : '' }}</small>
    </div>
    <div class="previews"></div>

    @if ($hint)<div class="hint">{{ $hint }}</div>@endif
    @error($key)<div class="error">⚠ {{ $message }}</div>@enderror
    @error($key.'.*')<div class="error">⚠ {{ $message }}</div>@enderror
</div>
