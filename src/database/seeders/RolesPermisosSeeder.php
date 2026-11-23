<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesPermisosSeeder extends Seeder
{
    /** Módulos del sistema y las acciones que admite cada uno. */
    public const MODULOS = [
        'dashboard' => ['ver'],
        'categorias' => ['ver', 'crear', 'editar', 'eliminar'],
        'productos' => ['ver', 'crear', 'editar', 'eliminar'],
        'proveedores' => ['ver', 'crear', 'editar', 'eliminar'],
        'clientes' => ['ver', 'crear', 'editar', 'eliminar'],
        'pedidos' => ['ver', 'crear', 'editar', 'eliminar'],
        'banners' => ['ver', 'crear', 'editar', 'eliminar'],
        'mensajes' => ['ver', 'eliminar'],
        'reportes' => ['ver', 'exportar'],
        'usuarios' => ['ver', 'crear', 'editar', 'eliminar'],
        'roles' => ['ver', 'crear', 'editar', 'eliminar'],
        'permisos' => ['ver', 'crear', 'editar', 'eliminar'],
        'bitacora' => ['ver'],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::MODULOS as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                Permission::firstOrCreate(['name' => "{$modulo}.{$accion}", 'guard_name' => 'web']);
            }
        }

        $todos = Permission::pluck('name');

        Role::firstOrCreate(['name' => 'Administrador'])->syncPermissions($todos);

        Role::firstOrCreate(['name' => 'Gerente'])->syncPermissions(
            $todos->reject(fn ($p) => str_starts_with($p, 'usuarios.') && $p !== 'usuarios.ver')
                ->reject(fn ($p) => str_starts_with($p, 'roles.') || str_starts_with($p, 'permisos.'))
        );

        Role::firstOrCreate(['name' => 'Vendedor'])->syncPermissions([
            'dashboard.ver', 'productos.ver', 'categorias.ver',
            'clientes.ver', 'clientes.crear', 'clientes.editar',
            'pedidos.ver', 'pedidos.crear', 'pedidos.editar',
            'reportes.ver',
        ]);

        Role::firstOrCreate(['name' => 'Almacenista'])->syncPermissions([
            'dashboard.ver',
            'productos.ver', 'productos.crear', 'productos.editar',
            'categorias.ver', 'categorias.crear', 'categorias.editar',
            'proveedores.ver', 'pedidos.ver',
        ]);
    }
}
