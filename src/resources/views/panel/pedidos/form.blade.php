@extends('layouts.panel')
@section('title', $pedido->exists ? 'Editar pedido' : 'Nuevo pedido')

@php
    $items = old('items', $pedido->exists
        ? $pedido->detalles->map(fn ($d) => ['producto_id' => $d->producto_id, 'cantidad' => $d->cantidad])->all()
        : [['producto_id' => '', 'cantidad' => 1]]);
    $previos = $pedido->exists ? $pedido->detalles->pluck('cantidad', 'producto_id') : collect();
    $catalogo = $productos->map(fn ($p) => ['id' => $p->id, 'texto' => "{$p->sku} · {$p->nombre}", 'precio' => (float) $p->precio,
        'stock' => $p->stock + (int) ($previos[$p->id] ?? 0)])->values();
@endphp

@section('content')
<x-encabezado eyebrow="Pedidos" :titulo="$pedido->exists ? 'Editar pedido '.$pedido->folio : 'Nuevo pedido '.$pedido->folio" subtitulo="El stock se valida al guardar. Al crear el pedido se envía la confirmación con PDF al cliente.">
    <a class="btn btn-outline" href="{{ route('panel.pedidos.index') }}">← Volver</a>
</x-encabezado>

<form class="js-validate" id="formPedido" method="POST" action="{{ $pedido->exists ? route('panel.pedidos.update', $pedido) : route('panel.pedidos.store') }}">
    @csrf
    @if ($pedido->exists) @method('PUT') @endif
    <div class="grid grid-3">
        <div class="card span-2">
            <div class="card-head"><h3>Productos</h3><button type="button" class="btn btn-outline btn-sm" id="agregarItem"><i data-lucide="plus"></i> Agregar renglón</button></div>
            <div class="table-wrap">
                <table class="table" id="tablaItems">
                    <thead><tr><th style="min-width:280px">Producto</th><th style="width:120px">Cantidad</th><th class="num">Precio</th><th class="num">Importe</th><th></th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            @error('items')<div class="alert alert-error" style="margin:14px">{{ $message }}</div>@enderror
            <div style="padding:18px 22px;display:flex;justify-content:flex-end">
                <dl class="detail-list" style="grid-template-columns:120px 140px;text-align:right">
                    <dt>Subtotal</dt><dd id="tSub">$0.00</dd>
                    <dt>IVA 16%</dt><dd id="tIva">$0.00</dd>
                    <dt style="font-size:16px;color:var(--navy)">Total</dt><dd id="tTot" style="font-size:18px">$0.00</dd>
                </dl>
            </div>
        </div>
        <div class="card card-pad">
            <div class="form-grid" style="grid-template-columns:1fr">
                <x-campo name="cliente_id" label="Cliente" type="select" :value="$pedido->cliente_id ?? request('cliente')" required :options="$clientes" />
                <x-campo name="fecha" label="Fecha del pedido" type="date" :value="$pedido->fecha?->format('Y-m-d')" required :max="now()->format('Y-m-d')" />
                <x-campo name="fecha_entrega" label="Fecha de entrega" type="date" :value="$pedido->fecha_entrega?->format('Y-m-d')" />
                <x-campo name="estado" label="Estado" type="select" :value="$pedido->estado" required :options="\App\Models\Pedido::ESTADOS" />
                <x-campo name="metodo_pago" label="Método de pago" type="select" :value="$pedido->metodo_pago" required :options="\App\Models\Pedido::METODOS_PAGO" />
                <x-campo name="notas" label="Notas" type="textarea" :value="$pedido->notas" maxlength="1000" contador />
            </div>
            <div class="form-actions">
                <button class="btn btn-yellow" type="submit" style="width:100%"><i data-lucide="save"></i> Guardar pedido</button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const catalogo = @json($catalogo);
    const iniciales = @json(array_values($items));
    const errores = @json($errors->getMessages());
    const tbody = document.querySelector('#tablaItems tbody');
    const mxn = v => '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const opciones = sel => '<option value="">Selecciona un producto</option>' + catalogo.map(p =>
        `<option value="${p.id}" ${String(sel) === String(p.id) ? 'selected' : ''} ${p.stock <= 0 && String(sel) !== String(p.id) ? 'disabled' : ''}>${p.texto} (stock ${p.stock})</option>`).join('');
    let idx = 0;

    function fila(item = { producto_id: '', cantidad: 1 }) {
        const i = idx++;
        const tr = document.createElement('tr');
        const errP = (errores[`items.${i}.producto_id`] || [])[0], errC = (errores[`items.${i}.cantidad`] || [])[0];
        tr.innerHTML = `
            <td><div class="field ${errP ? 'has-error' : ''}"><select name="items[${i}][producto_id]" required data-nombre="El producto">${opciones(item.producto_id)}</select>${errP ? `<div class="error">⚠ ${errP}</div>` : ''}</div></td>
            <td><div class="field ${errC ? 'has-error' : ''}"><input type="number" name="items[${i}][cantidad]" value="${item.cantidad}" min="1" max="10000" step="1" required data-nombre="La cantidad">${errC ? `<div class="error">⚠ ${errC}</div>` : ''}</div></td>
            <td class="num precio">—</td><td class="num importe"><b>—</b></td>
            <td><button type="button" class="btn btn-danger btn-icon quitar" title="Quitar">✕</button></td>`;
        tbody.appendChild(tr);
        tr.querySelector('.quitar').addEventListener('click', () => {
            if (tbody.children.length === 1) return Swal.fire({ icon: 'info', text: 'El pedido debe tener al menos un producto.', confirmButtonColor: '#1677ff' });
            tr.remove(); calcular();
        });
        tr.querySelectorAll('select,input').forEach(el => el.addEventListener('input', calcular));
        calcular();
    }

    function calcular() {
        let sub = 0; const usados = {};
        tbody.querySelectorAll('tr').forEach(tr => {
            const sel = tr.querySelector('select'), cant = tr.querySelector('input');
            const p = catalogo.find(x => String(x.id) === sel.value);
            const c = parseInt(cant.value || 0, 10);
            let msg = '';
            if (p) {
                if (usados[p.id]) msg = 'Producto repetido: ajusta la cantidad en un solo renglón.';
                usados[p.id] = true;
                const cancelado = document.querySelector('[name=estado]').value === 'cancelado';
                cant.setCustomValidity(!cancelado && c > p.stock ? `Stock insuficiente: hay ${p.stock} disponible(s).` : '');
            } else {
                cant.setCustomValidity('');
            }
            sel.setCustomValidity(msg);
            tr.querySelector('.precio').textContent = p ? mxn(p.precio) : '—';
            const imp = p ? p.precio * c : 0;
            tr.querySelector('.importe').innerHTML = `<b>${p ? mxn(imp) : '—'}</b>`;
            sub += imp;
        });
        document.getElementById('tSub').textContent = mxn(sub);
        document.getElementById('tIva').textContent = mxn(sub * .16);
        document.getElementById('tTot').textContent = mxn(sub * 1.16);
    }

    document.getElementById('agregarItem').addEventListener('click', () => fila());
    iniciales.forEach(fila);
});
</script>
@endpush
