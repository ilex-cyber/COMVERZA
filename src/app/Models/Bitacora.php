<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bitacora extends Model
{
    protected $table = 'bitacora';

    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'accion', 'modulo', 'descripcion', 'ip'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Registra una acción del usuario autenticado. */
    public static function registrar(string $accion, string $modulo, string $descripcion): void
    {
        static::create([
            'user_id' => auth()->id(),
            'accion' => $accion,
            'modulo' => $modulo,
            'descripcion' => mb_substr($descripcion, 0, 500),
            'ip' => request()?->ip(),
        ]);
    }
}
