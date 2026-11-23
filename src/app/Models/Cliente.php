<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'tipo', 'nombre', 'empresa', 'rfc', 'email', 'telefono', 'direccion',
        'ciudad', 'codigo_postal', 'limite_credito', 'activo',
    ];

    protected $casts = ['activo' => 'boolean', 'limite_credito' => 'decimal:2'];

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->empresa ? "{$this->empresa} ({$this->nombre})" : $this->nombre;
    }
}
