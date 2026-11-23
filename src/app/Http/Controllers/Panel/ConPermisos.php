<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Routing\Controllers\Middleware;

/**
 * Asigna automáticamente el permiso RBAC requerido a cada acción
 * de un controlador de recursos: {modulo}.ver|crear|editar|eliminar
 */
trait ConPermisos
{
    protected static function permisosDe(string $modulo): array
    {
        return [
            new Middleware("permission:{$modulo}.ver", only: ['index', 'show']),
            new Middleware("permission:{$modulo}.crear", only: ['create', 'store']),
            new Middleware("permission:{$modulo}.editar", only: ['edit', 'update']),
            new Middleware("permission:{$modulo}.eliminar", only: ['destroy']),
        ];
    }
}
