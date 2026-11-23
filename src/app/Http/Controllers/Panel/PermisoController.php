<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Database\Seeders\RolesPermisosSeeder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermisoController extends Controller implements HasMiddleware
{
    use ConPermisos;

    public static function middleware(): array
    {
        return self::permisosDe('permisos');
    }

    public function index(Request $request)
    {
        $permisos = Permission::withCount('roles')->with('roles:id,name')
            ->when($request->buscar, fn ($q, $b) => $q->where('name', 'like', "%{$b}%"))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('panel.permisos.index', compact('permisos'));
    }

    public function create()
    {
        return view('panel.permisos.form', ['permiso' => new Permission]);
    }

    public function store(Request $request)
    {
        $p = Permission::create(['name' => $this->validar($request), 'guard_name' => 'web']);
        Bitacora::registrar('crear', 'permisos', "Creó el permiso {$p->name}");

        return redirect()->route('panel.permisos.index')->with('success', "Permiso «{$p->name}» creado. Asígnalo a un rol para que tenga efecto.");
    }

    public function edit(Permission $permiso)
    {
        if ($this->esDelSistema($permiso)) {
            return redirect()->route('panel.permisos.index')->with('error', "«{$permiso->name}» es un permiso del sistema y no se puede editar.");
        }

        return view('panel.permisos.form', compact('permiso'));
    }

    public function update(Request $request, Permission $permiso)
    {
        if ($this->esDelSistema($permiso)) {
            return redirect()->route('panel.permisos.index')->with('error', 'Los permisos del sistema no se pueden editar.');
        }
        $permiso->update(['name' => $this->validar($request, $permiso)]);
        Bitacora::registrar('editar', 'permisos', "Renombró un permiso a {$permiso->name}");

        return redirect()->route('panel.permisos.index')->with('success', 'Permiso actualizado.');
    }

    public function destroy(Permission $permiso)
    {
        if ($this->esDelSistema($permiso)) {
            return back()->with('error', "«{$permiso->name}» es un permiso del sistema y no se puede eliminar.");
        }
        if (($n = $permiso->roles()->count()) > 0) {
            return back()->with('error', "No se puede eliminar «{$permiso->name}»: está asignado a {$n} rol(es). Quítalo de los roles primero.");
        }
        $permiso->delete();
        Bitacora::registrar('eliminar', 'permisos', "Eliminó el permiso {$permiso->name}");

        return redirect()->route('panel.permisos.index')->with('success', 'Permiso eliminado.');
    }

    public static function esDelSistema(Permission $p): bool
    {
        [$modulo, $accion] = array_pad(explode('.', $p->name, 2), 2, null);

        return in_array($accion, RolesPermisosSeeder::MODULOS[$modulo] ?? [], true);
    }

    private function validar(Request $request, ?Permission $p = null): string
    {
        $request->merge(['name' => strtolower(trim((string) $request->name))]);

        return $request->validate([
            'name' => ['required', 'max:60', 'regex:/^[a-z_]+\.[a-z_]+$/', Rule::unique('permissions', 'name')->ignore($p)],
        ], ['name.regex' => 'Usa el formato modulo.accion en minúsculas (ej. inventario.ajustar).'])['name'];
    }
}
