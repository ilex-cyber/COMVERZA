<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    protected $fillable = ['titulo', 'subtitulo', 'imagen', 'enlace', 'orden', 'activo'];

    protected $casts = ['activo' => 'boolean', 'orden' => 'integer'];

    public function getImagenUrlAttribute(): string
    {
        return asset($this->imagen);
    }
}
