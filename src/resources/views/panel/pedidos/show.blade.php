@extends('layouts.panel')
@section('title', 'Pedido '.$pedido->folio)

@section('content')
<x-encabezado eyebrow="Pedido" :titulo="$pedido->folio" :subtitulo="$pedido->cliente->nombre_completo.' · '.$pedido->fecha->format('d/m/Y')">
    <a class="btn btn-outline" href="{{ route('panel.pedidos.index') }}">← Volver</a>
    <a class="btn btn-outline" href="{{ route('panel.pedidos.pdf', $pedido) }}" target="_blank"><i data-lucide="file-text"></i> PDF</a>
    <form method="POST" action="{{ route('panel.pedidos.enviar', $pedido) }}" class="js-confirm" data-titulo="¿Enviar el pedido por correo?" data-mensaje="Se enviará el PDF a {{ $pedido->cliente->email }}" data-boton="Sí, enviar">
        @csrf <button class="btn btn-blue"><i data-lucide="send"></i> Enviar al cliente</button>
    </form>
    @can('pedidos.editar')@if ($pedido->editable)<a class="btn btn-yellow" href="{{ route('panel.pedidos.edit', $pedido) }}"><i data-lucide="pencil"></i> Editar</a>@endif @endcan
</x-encabezado>

<div class="grid grid-3">
    <div class="card span-2">
        <div class="card-head"><h3>Detalle</h3><span class="badge badge-{{ $pedido->estado }}">{{ $pedido->estado_texto }}</span></div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Producto</th><th class="num">Cantidad</th><th class="num">Precio</th><th class="num">Importe</th></tr></thead>
                <tbody>
                @foreach ($pedido->detalles as $d)
                    <tr><td><div class="cell-main"><img src="{{ $d->producto->imagen_url }}" alt=""><div><strong>{{ $d->producto->nombre }}</strong><small>{{ $d->producto->sku }}</small></div></div></td>
                        <td class="num">{{ $d->cantidad }}</td><td class="num">${{ number_format($d->precio_unitario, 2) }}</td><td class="num"><b>${{ number_format($d->importe, 2) }}</b></td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:18px 22px;display:flex;justify-content:flex-end">
            <dl class="detail-list" style="grid-template-columns:120px 150px;text-align:right">
                <dt>Subtotal</dt><dd>${{ number_format($pedido->subtotal, 2) }}</dd>
                <dt>IVA 16%</dt><dd>${{ number_format($pedido->iva, 2) }}</dd>
                <dt>Total</dt><dd style="font-size:18px">${{ number_format($pedido->total, 2) }}</dd>
            </dl>
        </div>
    </div>
    <div class="card card-pad">
        <dl class="detail-list">
            <dt>Cliente</dt><dd>@can('clientes.ver')<a class="link" href="{{ route('panel.clientes.show', $pedido->cliente) }}">{{ $pedido->cliente->nombre_completo }}</a>@else{{ $pedido->cliente->nombre_completo }}@endcan</dd>
            <dt>Correo</dt><dd>{{ $pedido->cliente->email }}</dd>
            <dt>Entrega</dt><dd>{{ $pedido->fecha_entrega?->format('d/m/Y') ?? '—' }}</dd>
            <dt>Pago</dt><dd>{{ \App\Models\Pedido::METODOS_PAGO[$pedido->metodo_pago] }}</dd>
            <dt>Vendedor</dt><dd>{{ $pedido->usuario?->name ?? '—' }}</dd>
            <dt>Notas</dt><dd>{{ $pedido->notas ?: '—' }}</dd>
        </dl>
    </div>
</div>
@endsection
