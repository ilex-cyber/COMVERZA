<?php

namespace App\Models;

use App\Notifications\RestablecerPassword;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'telefono', 'puesto', 'avatar', 'activo', 'ultimo_acceso',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'ultimo_acceso' => 'datetime',
            'activo' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset($this->avatar)
            : 'https://ui-avatars.com/api/?background=1677ff&color=fff&bold=true&name='.urlencode($this->name);
    }

    public function getInicialesAttribute(): string
    {
        return collect(explode(' ', $this->name))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
    }

    /** Correo de recuperación de contraseña en español y con la marca. */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new RestablecerPassword($token));
    }
}
