<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'sku', 'nombre', 'slug', 'descripcion', 'categoria_id', 'proveedor_id', 'costo', 'precio',
        'stock', 'stock_minimo', 'unidad', 'imagen', 'ficha_tecnica', 'destacado', 'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'costo' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'destacado' => 'boolean',
        'activo' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Producto $p) {
            $p->slug = Str::slug($p->nombre.'-'.$p->sku);
        });
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ProductoImagen::class)->orderBy('orden');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class);
    }

    public function getImagenUrlAttribute(): string
    {
        return $this->imagen ? asset($this->imagen) : asset('images/producto-placeholder.svg');
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function getMargenAttribute(): float
    {
        return $this->precio > 0 ? round((($this->precio - $this->costo) / $this->precio) * 100, 1) : 0;
    }

    public function scopeActivos($q)
    {
        return $q->where('activo', true);
    }
}
