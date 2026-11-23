<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolController extends Controller implements HasMiddleware
{
    use ConPermisos;

    /** Roles base del sistema: no se pueden renombrar ni eliminar. */
    public const PROTEGIDOS = ['Administrador'];

    public static function middleware(): array
    {
        return self::permisosDe('roles');
    }

    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->orderBy('name')->get();

        return view('panel.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('panel.roles.form', ['rol' => new Role, 'grupos' => $this->grupos(), 'asignados' => []]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $rol = Role::create(['name' => $datos['name'], 'guard_name' => 'web']);
        $rol->syncPermissions($datos['permisos'] ?? []);
        Bitacora::registrar('crear', 'roles', "Creó el rol {$rol->name} con ".count($datos['permisos'] ?? []).' permisos');

        return redirect()->route('panel.roles.index')->with('success', "Rol «{$rol->name}» creado.");
    }

    public function edit(Role $rol)
    {
        return view('panel.roles.form', ['rol' => $rol, 'grupos' => $this->grupos(), 'asignados' => $rol->permissions->pluck('name')->all()]);
    }

    public function update(Request $request, Role $rol)
    {
        $datos = $this->validar($request, $rol);
        if (in_array($rol->name, self::PROTEGIDOS) && $datos['name'] !== $rol->name) {
            return back()->withInput()->with('error', "El rol «{$rol->name}» es del sistema y no se puede renombrar.");
        }
        $rol->update(['name' => $datos['name']]);
        $rol->syncPermissions($datos['permisos'] ?? []);
        Bitacora::registrar('editar', 'roles', "Actualizó el rol {$rol->name} (".count($datos['permisos'] ?? []).' permisos)');

        return redirect()->route('panel.roles.index')->with('success', "Rol «{$rol->name}» actualizado.");
    }

    public function destroy(Role $rol)
    {
        if (in_array($rol->name, self::PROTEGIDOS)) {
            return back()->with('error', "El rol «{$rol->name}» es del sistema y no se puede eliminar.");
        }
        if (($n = $rol->users()->count()) > 0) {
            return back()->with('error', "No se puede eliminar «{$rol->name}»: está asignado a {$n} usuario(s).");
        }
        $rol->delete();
        Bitacora::registrar('eliminar', 'roles', "Eliminó el rol {$rol->name}");

        return redirect()->route('panel.roles.index')->with('success', 'Rol eliminado.');
    }

    private function validar(Request $request, ?Role $rol = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\s]+$/u', Rule::unique('roles', 'name')->ignore($rol)],
            'permisos' => ['nullable', 'array'],
            'permisos.*' => [Rule::exists('permissions', 'name')],
        ], ['name.regex' => 'El nombre del rol solo puede contener letras y espacios.']);
    }

    private function grupos()
    {
        return Permission::orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0]);
    }
}
