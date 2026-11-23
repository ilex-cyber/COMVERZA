<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Validation\Rule;

class ClienteController extends Controller implements HasMiddleware
{
    use ConPermisos;

    public static function middleware(): array
    {
        return self::permisosDe('clientes');
    }

    public function index(Request $request)
    {
        $clientes = Cliente::withCount('pedidos')
            ->withSum(['pedidos as total_comprado' => fn ($q) => $q->where('estado', '!=', 'cancelado')], 'total')
            ->when($request->buscar, fn ($q, $b) => $q->where(fn ($w) => $w->where('nombre', 'like', "%{$b}%")
                ->orWhere('empresa', 'like', "%{$b}%")->orWhere('email', 'like', "%{$b}%")->orWhere('rfc', 'like', "%{$b}%")))
            ->when($request->tipo, fn ($q, $t) => $q->where('tipo', $t))
            ->orderBy('nombre')->paginate(12)->withQueryString();

        return view('panel.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('panel.clientes.form', ['cliente' => new Cliente(['tipo' => 'Empresa', 'activo' => true, 'limite_credito' => 0])]);
    }

    public function store(Request $request)
    {
        $c = Cliente::create($this->validar($request));
        Bitacora::registrar('crear', 'clientes', "Registró al cliente {$c->nombre}");

        return redirect()->route('panel.clientes.show', $c)->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $pedidos = $cliente->pedidos()->latest('fecha')->paginate(8);
        $resumen = [
            'total' => $cliente->pedidos()->where('estado', '!=', 'cancelado')->sum('total'),
            'pedidos' => $cliente->pedidos()->count(),
            'ultimo' => $cliente->pedidos()->max('fecha'),
        ];

        return view('panel.clientes.show', compact('cliente', 'pedidos', 'resumen'));
    }

    public function edit(Cliente $cliente)
    {
        return view('panel.clientes.form', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($this->validar($request, $cliente));
        Bitacora::registrar('editar', 'clientes', "Editó al cliente {$cliente->nombre}");

        return redirect()->route('panel.clientes.show', $cliente)->with('success', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        $total = $cliente->pedidos()->count();
        if ($total > 0) {
            return back()->with('error', "No se puede eliminar a «{$cliente->nombre}»: tiene {$total} pedido(s) en su historial. Puedes desactivarlo.");
        }

        $cliente->delete();
        Bitacora::registrar('eliminar', 'clientes', "Eliminó al cliente {$cliente->nombre}");

        return redirect()->route('panel.clientes.index')->with('success', 'Cliente eliminado.');
    }

    private function validar(Request $request, ?Cliente $c = null): array
    {
        $request->merge(['rfc' => $request->filled('rfc') ? strtoupper(trim($request->rfc)) : null]);

        $datos = $request->validate([
            'tipo' => ['required', 'in:Empresa,Persona'],
            'nombre' => ['required', 'string', 'min:3', 'max:120', 'regex:/^[\pL\s\.]+$/u'],
            'empresa' => ['nullable', 'required_if:tipo,Empresa', 'string', 'max:150'],
            'rfc' => ['nullable', 'required_if:tipo,Empresa', 'regex:'.ProveedorController::RFC, Rule::unique('clientes', 'rfc')->ignore($c)],
            'email' => ['required', 'email', 'max:150', Rule::unique('clientes', 'email')->ignore($c)],
            'telefono' => ['required', 'regex:/^\d{10}$/'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:80'],
            'codigo_postal' => ['nullable', 'digits:5'],
            'limite_credito' => ['required', 'numeric', 'min:0', 'max:9999999'],
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'rfc.regex' => 'El RFC no tiene un formato válido (ej. ABC260101XY1).',
            'telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos.',
            'empresa.required_if' => 'La empresa es obligatoria para clientes de tipo Empresa.',
            'rfc.required_if' => 'El RFC es obligatorio para clientes de tipo Empresa.',
        ]);
        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}
