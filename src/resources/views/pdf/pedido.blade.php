<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Pedido {{ $pedido->folio }}</title>@include('pdf._estilos')</head>
<body>
<div class="footer">COMVERZA · Tecnología que impulsa tu negocio · Documento generado el {{ now()->format('d/m/Y H:i') }}</div>
<div class="head"><table><tr>
    <td><div class="brand">COMVER<span>ZA</span></div><div class="muted">Business Platform</div></td>
    <td style="text-align:right"><div style="font-size:16px;font-weight:bold">PEDIDO {{ $pedido->folio }}</div><div class="muted">Fecha: {{ $pedido->fecha->format('d/m/Y') }}</div></td>
</tr></table></div>

<table style="width:100%"><tr>
    <td style="width:55%;vertical-align:top" class="box">
        <div class="muted">CLIENTE</div>
        <b style="font-size:13px">{{ $pedido->cliente->empresa ?? $pedido->cliente->nombre }}</b><br>
        {{ $pedido->cliente->nombre }}<br>
        @if ($pedido->cliente->rfc)RFC: {{ $pedido->cliente->rfc }}<br>@endif
        {{ $pedido->cliente->email }} · {{ $pedido->cliente->telefono }}<br>
        {{ $pedido->cliente->direccion }} {{ $pedido->cliente->ciudad }}
    </td>
    <td style="width:4%"></td>
    <td style="vertical-align:top" class="box">
        <div class="muted">DATOS DEL PEDIDO</div>
        Estado: <span class="badge">{{ $pedido->estado_texto }}</span><br>
        Método de pago: {{ \App\Models\Pedido::METODOS_PAGO[$pedido->metodo_pago] }}<br>
        Entrega estimada: {{ $pedido->fecha_entrega?->format('d/m/Y') ?? 'Por confirmar' }}<br>
        Atendió: {{ $pedido->usuario?->name ?? 'COMVERZA' }}
    </td>
</tr></table>

<h2>Detalle</h2>
<table class="t">
    <thead><tr><th>SKU</th><th>Producto</th><th class="num">Cant.</th><th class="num">Precio unit.</th><th class="num">Importe</th></tr></thead>
    <tbody>
    @foreach ($pedido->detalles as $d)
        <tr><td>{{ $d->producto->sku }}</td><td>{{ $d->producto->nombre }}</td><td class="num">{{ $d->cantidad }}</td>
            <td class="num">${{ number_format($d->precio_unitario, 2) }}</td><td class="num">${{ number_format($d->importe, 2) }}</td></tr>
    @endforeach
    </tbody>
</table>

<table style="width:40%;margin-left:60%;margin-top:12px" class="t">
    <tr><td>Subtotal</td><td class="num">${{ number_format($pedido->subtotal, 2) }}</td></tr>
    <tr><td>IVA 16%</td><td class="num">${{ number_format($pedido->iva, 2) }}</td></tr>
    <tr><td style="background:#06112f;color:#ffd533;font-weight:bold">TOTAL MXN</td><td class="num" style="background:#06112f;color:#fff;font-weight:bold;font-size:13px">${{ number_format($pedido->total, 2) }}</td></tr>
</table>

@if ($pedido->notas)<p class="box" style="margin-top:16px"><b>Notas:</b> {{ $pedido->notas }}</p>@endif
<p class="muted" style="margin-top:22px">Este documento es una nota de pedido informativa y no sustituye a un comprobante fiscal (CFDI).</p>
</body></html>
