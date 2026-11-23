<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'razon_social', 'rfc', 'contacto', 'email', 'telefono', 'direccion', 'ciudad',
        'sitio_web', 'logo', 'constancia_fiscal', 'calificacion', 'activo',
    ];

    protected $casts = ['activo' => 'boolean', 'calificacion' => 'integer'];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset($this->logo) : null;
    }
}
