<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = ['nombre', 'slug', 'descripcion', 'color', 'imagen', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(fn (Categoria $c) => $c->slug = Str::slug($c->nombre));
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function scopeActivas($q)
    {
        return $q->where('activo', true);
    }
}
