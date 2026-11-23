@props([
    'name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null,
    'options' => null, 'placeholder' => null, 'full' => false, 'maxlength' => null, 'minlength' => null,
    'min' => null, 'max' => null, 'step' => null, 'pattern' => null, 'patron' => null, 'prefix' => null,
    'vacio' => 'Selecciona una opción', 'contador' => false,
])
@php
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $actual = old($key, $value);
    $id = 'f_'.str_replace(['[', ']', '.'], '_', $name);
    $nombreHumano = ucfirst(strtolower(strip_tags($label)));
    $extra = collect([
        'maxlength' => $maxlength, 'minlength' => $minlength, 'min' => $min, 'max' => $max,
        'step' => $step, 'pattern' => $pattern, 'placeholder' => $placeholder,
    ])->filter(fn ($v) => $v !== null)->map(fn ($v, $k) => $k.'="'.e($v).'"')->implode(' ');
@endphp
<div {{ $attributes->class(['field', 'full' => $full, 'has-error' => $errors->has($key)]) }}>
    <label for="{{ $id }}">{{ $label }} @if ($required)<span class="req">*</span>@endif</label>

    @if ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" data-nombre="{{ $nombreHumano }}" @required($required)>
            <option value="">{{ $vacio }}</option>
            @foreach ($options as $val => $texto)
                <option value="{{ $val }}" @selected((string) $actual === (string) $val)>{{ $texto }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" data-nombre="{{ $nombreHumano }}" @required($required) {!! $extra !!} @if($contador) data-maxlength-counter @endif>{{ $actual }}</textarea>
    @elseif ($prefix)
        <div class="input-group">
            <span>{{ $prefix }}</span>
            <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $actual }}" data-nombre="{{ $nombreHumano }}" @required($required) {!! $extra !!} @if($patron) data-patron="{{ $patron }}" @endif>
        </div>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $actual }}" data-nombre="{{ $nombreHumano }}" @required($required) {!! $extra !!} @if($patron) data-patron="{{ $patron }}" @endif @if($contador) data-maxlength-counter @endif>
    @endif

    @if ($hint)<div class="hint">{{ $hint }}</div>@endif
    @error($key)<div class="error">⚠ {{ $message }}</div>@enderror
</div>
