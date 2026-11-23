<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoDetalle extends Model
{
    protected $table = 'pedido_detalles';

    protected $fillable = ['pedido_id', 'producto_id', 'cantidad', 'precio_unitario', 'importe'];

    protected $casts = ['precio_unitario' => 'decimal:2', 'importe' => 'decimal:2'];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
