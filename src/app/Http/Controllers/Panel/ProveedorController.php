<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Proveedor;
use App\Support\Archivos;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller implements HasMiddleware
{
    use ConPermisos;

    /** RFC mexicano: 3-4 letras, fecha AAMMDD y homoclave de 3 caracteres. */
    public const RFC = '/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/';

    public static function middleware(): array
    {
        return self::permisosDe('proveedores');
    }

    public function index(Request $request)
    {
        $proveedores = Proveedor::withCount('productos')
            ->when($request->buscar, fn ($q, $b) => $q->where(fn ($w) => $w->where('razon_social', 'like', "%{$b}%")
                ->orWhere('rfc', 'like', "%{$b}%")->orWhere('contacto', 'like', "%{$b}%")))
            ->when($request->ciudad, fn ($q, $c) => $q->where('ciudad', $c))
            ->orderBy('razon_social')->paginate(10)->withQueryString();
        $ciudades = Proveedor::whereNotNull('ciudad')->distinct()->orderBy('ciudad')->pluck('ciudad');

        return view('panel.proveedores.index', compact('proveedores', 'ciudades'));
    }

    public function create()
    {
        return view('panel.proveedores.form', ['proveedor' => new Proveedor(['calificacion' => 5, 'activo' => true])]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos = $this->archivos($request, $datos);
        $p = Proveedor::create($datos);
        Bitacora::registrar('crear', 'proveedores', "Registró al proveedor {$p->razon_social}");

        return redirect()->route('panel.proveedores.show', $p)->with('success', 'Proveedor registrado correctamente.');
    }

    public function show(Proveedor $proveedor)
    {
        $proveedor->load(['productos' => fn ($q) => $q->with('categoria')->orderBy('nombre')]);

        return view('panel.proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        return view('panel.proveedores.form', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $datos = $this->validar($request, $proveedor);
        $datos = $this->archivos($request, $datos, $proveedor);
        $proveedor->update($datos);
        Bitacora::registrar('editar', 'proveedores', "Editó al proveedor {$proveedor->razon_social}");

        return redirect()->route('panel.proveedores.show', $proveedor)->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $total = $proveedor->productos()->count();
        if ($total > 0) {
            return back()->with('error', "No se puede eliminar a «{$proveedor->razon_social}»: surte {$total} producto(s). Desactívalo o reasigna sus productos.");
        }

        Archivos::eliminar($proveedor->logo);
        Archivos::eliminar($proveedor->constancia_fiscal);
        $proveedor->delete();
        Bitacora::registrar('eliminar', 'proveedores', "Eliminó al proveedor {$proveedor->razon_social}");

        return redirect()->route('panel.proveedores.index')->with('success', 'Proveedor eliminado.');
    }

    private function validar(Request $request, ?Proveedor $p = null): array
    {
        $request->merge(['rfc' => strtoupper(trim((string) $request->rfc))]);

        $datos = $request->validate([
            'razon_social' => ['required', 'string', 'min:3', 'max:150'],
            'rfc' => ['required', 'regex:'.self::RFC, Rule::unique('proveedores', 'rfc')->ignore($p)],
            'contacto' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s\.]+$/u'],
            'email' => ['required', 'email', 'max:150', Rule::unique('proveedores', 'email')->ignore($p)],
            'telefono' => ['required', 'regex:/^\d{10}$/'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:80'],
            'sitio_web' => ['nullable', 'url', 'max:150'],
            'calificacion' => ['required', 'integer', 'between:1,5'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'constancia_fiscal' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'rfc.regex' => 'El RFC no tiene un formato válido (ej. ABC260101XY1).',
            'telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos.',
            'contacto.regex' => 'El nombre de contacto solo puede contener letras.',
        ]);
        $datos['activo'] = $request->boolean('activo');
        unset($datos['logo'], $datos['constancia_fiscal']);

        return $datos;
    }

    private function archivos(Request $request, array $datos, ?Proveedor $p = null): array
    {
        if ($request->hasFile('logo')) {
            Archivos::eliminar($p?->logo);
            $datos['logo'] = Archivos::imagen($request->file('logo'), 'proveedores', 500);
        }
        if ($request->hasFile('constancia_fiscal')) {
            Archivos::eliminar($p?->constancia_fiscal);
            $datos['constancia_fiscal'] = Archivos::documento($request->file('constancia_fiscal'), 'documentos');
        }

        return $datos;
    }
}
