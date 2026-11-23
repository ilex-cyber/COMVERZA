<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Inventario</title>@include('pdf._estilos')</head>
<body>
<div class="footer">COMVERZA · Reporte de inventario · Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name }}</div>
<div class="head"><table><tr>
    <td><div class="brand">COMVER<span>ZA</span></div><div class="muted">Business Platform</div></td>
    <td style="text-align:right"><div style="font-size:16px;font-weight:bold">INVENTARIO DE PRODUCTOS</div><div class="muted">{{ $productos->count() }} productos · valor ${{ number_format($productos->sum(fn ($p) => $p->stock * $p->costo), 2) }}</div></td>
</tr></table></div>

<table class="t">
    <thead><tr><th>SKU</th><th>Producto</th><th>Categoría</th><th>Proveedor</th><th class="num">Costo</th><th class="num">Precio</th><th class="num">Margen</th><th class="num">Stock</th><th>Estado</th></tr></thead>
    <tbody>
    @foreach ($productos as $p)
        <tr>
            <td>{{ $p->sku }}</td><td>{{ $p->nombre }}</td><td>{{ $p->categoria->nombre }}</td><td>{{ $p->proveedor?->razon_social ?? '—' }}</td>
            <td class="num">${{ number_format($p->costo, 2) }}</td><td class="num">${{ number_format($p->precio, 2) }}</td><td class="num">{{ $p->margen }}%</td>
            <td class="num" style="{{ $p->stock_bajo ? 'color:#f04438;font-weight:bold' : '' }}">{{ $p->stock }}</td>
            <td>{{ $p->activo ? 'Activo' : 'Inactivo' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body></html>
