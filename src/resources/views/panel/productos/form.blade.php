@extends('layouts.panel')
@section('title', $producto->exists ? 'Editar producto' : 'Nuevo producto')

@section('content')
<x-encabezado eyebrow="Productos" :titulo="$producto->exists ? 'Editar «'.$producto->nombre.'»' : 'Nuevo producto'" subtitulo="El precio no puede ser menor que el costo. Las imágenes se optimizan automáticamente.">
    <a class="btn btn-outline" href="{{ route('panel.productos.index') }}">← Volver</a>
</x-encabezado>

<form class="js-validate" method="POST" enctype="multipart/form-data"
      action="{{ $producto->exists ? route('panel.productos.update', $producto) : route('panel.productos.store') }}">
    @csrf
    @if ($producto->exists) @method('PUT') @endif
    <div class="grid grid-3">
        <div class="card card-pad span-2">
            <div class="form-grid">
                <x-campo name="sku" label="SKU" :value="$producto->sku" required maxlength="30" pattern="[A-Za-z0-9\-]{3,30}" patron="Solo letras, números y guiones (3 a 30)." hint="Código único del producto." />
                <x-campo name="nombre" label="Nombre" :value="$producto->nombre" required minlength="3" maxlength="150" />
                <x-campo name="categoria_id" label="Categoría" type="select" :value="$producto->categoria_id" required :options="$categorias" />
                <x-campo name="proveedor_id" label="Proveedor" type="select" :value="$producto->proveedor_id" :options="$proveedores" vacio="Sin proveedor" />
                <x-campo name="costo" label="Costo" type="number" :value="$producto->costo" required min="0" max="9999999" step="0.01" prefix="$" />
                <x-campo name="precio" label="Precio de venta" type="number" :value="$producto->precio" required min="0.01" max="9999999" step="0.01" prefix="$" hint="Margen estimado: —" />
                <x-campo name="stock" label="Stock" type="number" :value="$producto->stock" required min="0" max="1000000" step="1" />
                <x-campo name="stock_minimo" label="Stock mínimo" type="number" :value="$producto->stock_minimo" required min="0" max="100000" step="1" hint="Se envía alerta por correo al llegar a este nivel." />
                <x-campo name="unidad" label="Unidad" type="select" :value="$producto->unidad" required :options="$unidades" />
                <div class="field" style="align-self:end;display:flex;gap:22px;flex-wrap:wrap">
                    <label class="switch"><input type="checkbox" name="activo" value="1" @checked(old('activo', $producto->activo))> Activo</label>
                    <label class="switch"><input type="checkbox" name="destacado" value="1" @checked(old('destacado', $producto->destacado))> Destacado en la web</label>
                </div>
                <x-campo name="descripcion" label="Descripción" type="textarea" :value="$producto->descripcion" maxlength="2000" contador full />
            </div>
        </div>
        <div class="card card-pad">
            <div class="form-grid" style="grid-template-columns:1fr">
                <x-archivo name="imagen" label="Imagen principal" :actual="$producto->imagen" min-width="300" hint="Mínimo 300 × 300 px." />
                <x-archivo name="imagenes[]" label="Galería (hasta 6 imágenes)" multiple max-files="6" />
                @if ($producto->exists && $producto->imagenes->count())
                    <div class="previews">
                        @foreach ($producto->imagenes as $img)
                            <div class="preview"><img src="{{ asset($img->ruta) }}" alt="">
                                <button type="button" class="remove" title="Quitar" data-eliminar-imagen="{{ route('panel.productos.imagenes.destroy', [$producto, $img]) }}">✕</button></div>
                        @endforeach
                    </div>
                @endif
                <x-archivo name="ficha_tecnica" label="Ficha técnica" accept="application/pdf,.pdf" tipos="Solo PDF" max-mb="5" :actual="$producto->ficha_tecnica" :actual-es-imagen="false" />
            </div>
        </div>
    </div>
    <div class="card card-pad" style="margin-top:20px">
        <div class="form-actions" style="border:0;margin:0;padding:0">
            <a class="btn btn-outline" href="{{ route('panel.productos.index') }}">Cancelar</a>
            <button class="btn btn-yellow" type="submit"><i data-lucide="save"></i> Guardar producto</button>
        </div>
    </div>
</form>

<form id="formEliminarImagen" method="POST" class="js-confirm" data-titulo="¿Quitar esta imagen de la galería?" style="display:none">@csrf @method('DELETE')</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const costo = document.querySelector('[name=costo]'), precio = document.querySelector('[name=precio]');
    const hint = precio.closest('.field').querySelector('.hint');
    const calc = () => {
        const c = parseFloat(costo.value) || 0, p = parseFloat(precio.value) || 0;
        precio.setCustomValidity(p && p < c ? 'El precio de venta no puede ser menor que el costo.' : '');
        hint.textContent = p > 0 ? `Margen estimado: ${(((p - c) / p) * 100).toFixed(1)}%` : 'Margen estimado: —';
    };
    costo.addEventListener('input', calc); precio.addEventListener('input', calc); calc();

    document.querySelectorAll('[data-eliminar-imagen]').forEach(b => b.addEventListener('click', () => {
        const f = document.getElementById('formEliminarImagen');
        f.action = b.dataset.eliminarImagen; f.requestSubmit();
    }));
});
</script>
@endpush
