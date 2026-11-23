<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Categoria;
use App\Support\Archivos;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller implements HasMiddleware
{
    use ConPermisos;

    public static function middleware(): array
    {
        return self::permisosDe('categorias');
    }

    public function index(Request $request)
    {
        $categorias = Categoria::withCount('productos')
            ->when($request->buscar, fn ($q, $b) => $q->where('nombre', 'like', "%{$b}%"))
            ->when($request->estado !== null && $request->estado !== '', fn ($q) => $q->where('activo', $request->estado === '1'))
            ->orderBy('nombre')->paginate(10)->withQueryString();

        return view('panel.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('panel.categorias.form', ['categoria' => new Categoria(['color' => '#1677ff', 'activo' => true])]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        if ($request->hasFile('imagen')) {
            $datos['imagen'] = Archivos::imagen($request->file('imagen'), 'categorias', 800);
        }
        $c = Categoria::create($datos);
        Bitacora::registrar('crear', 'categorias', "Creó la categoría {$c->nombre}");

        return redirect()->route('panel.categorias.index')->with('success', "Categoría «{$c->nombre}» creada.");
    }

    public function edit(Categoria $categoria)
    {
        return view('panel.categorias.form', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $datos = $this->validar($request, $categoria);

        // No se puede desactivar una categoría con productos activos
        if (! $datos['activo'] && $categoria->productos()->where('activo', true)->exists()) {
            return back()->withInput()->withErrors(['activo' => 'No puedes desactivar la categoría: tiene productos activos.']);
        }

        if ($request->hasFile('imagen')) {
            Archivos::eliminar($categoria->imagen);
            $datos['imagen'] = Archivos::imagen($request->file('imagen'), 'categorias', 800);
        }
        $categoria->update($datos);
        Bitacora::registrar('editar', 'categorias', "Editó la categoría {$categoria->nombre}");

        return redirect()->route('panel.categorias.index')->with('success', "Categoría «{$categoria->nombre}» actualizada.");
    }

    public function destroy(Categoria $categoria)
    {
        $total = $categoria->productos()->count();
        if ($total > 0) {
            return back()->with('error', "No se puede eliminar «{$categoria->nombre}»: tiene {$total} producto(s) asignado(s). Reasígnalos primero.");
        }

        Archivos::eliminar($categoria->imagen);
        $categoria->delete();
        Bitacora::registrar('eliminar', 'categorias', "Eliminó la categoría {$categoria->nombre}");

        return redirect()->route('panel.categorias.index')->with('success', 'Categoría eliminada.');
    }

    private function validar(Request $request, ?Categoria $categoria = null): array
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:80', Rule::unique('categorias', 'nombre')->ignore($categoria)],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'color' => ['required', 'hex_color'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=200,min_height=200'],
        ]);
        $datos['activo'] = $request->boolean('activo');
        unset($datos['imagen']);

        return $datos;
    }
}
