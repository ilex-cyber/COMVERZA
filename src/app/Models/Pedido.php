<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $table = 'pedidos';

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'en_proceso' => 'En proceso',
        'enviado' => 'Enviado',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
    ];

    public const METODOS_PAGO = [
        'transferencia' => 'Transferencia',
        'tarjeta' => 'Tarjeta',
        'efectivo' => 'Efectivo',
        'credito' => 'Crédito',
    ];

    public const IVA = 0.16;

    protected $fillable = [
        'folio', 'cliente_id', 'user_id', 'fecha', 'fecha_entrega', 'estado', 'metodo_pago',
        'subtotal', 'iva', 'total', 'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_entrega' => 'date',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class);
    }

    public function getEstadoTextoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getEditableAttribute(): bool
    {
        return in_array($this->estado, ['pendiente', 'en_proceso']);
    }

    public static function siguienteFolio(): string
    {
        $ultimo = (int) static::max('id');

        return 'CV-'.str_pad((string) (1001 + $ultimo), 5, '0', STR_PAD_LEFT);
    }

    public function recalcularTotales(): void
    {
        $subtotal = (float) $this->detalles()->sum('importe');
        $this->subtotal = $subtotal;
        $this->iva = round($subtotal * self::IVA, 2);
        $this->total = round($subtotal + $this->iva, 2);
        $this->save();
    }
}
