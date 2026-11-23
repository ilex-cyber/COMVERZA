<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.comverza');

        // El rol Administrador tiene acceso total aunque se agreguen permisos nuevos.
        Gate::before(fn ($user) => $user->hasRole('Administrador') ? true : null);
    }
}
