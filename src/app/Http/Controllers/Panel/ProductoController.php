<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\Proveedor;
use App\Support\Archivos;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Validation\Rule;

class ProductoController extends Controller implements HasMiddleware
{
    use ConPermisos;

    public const MAX_GALERIA = 6;

    public static function middleware(): array
    {
        return self::permisosDe('productos');
    }

    public function index(Request $request)
    {
        $productos = Producto::with(['categoria', 'proveedor'])
            ->when($request->buscar, fn ($q, $b) => $q->where(fn ($w) => $w->where('nombre', 'like', "%{$b}%")->orWhere('sku', 'like', "%{$b}%")))
            ->when($request->categoria, fn ($q, $c) => $q->where('categoria_id', $c))
            ->when($request->stock === 'bajo', fn ($q) => $q->whereColumn('stock', '<=', 'stock_minimo'))
            ->orderBy('nombre')->paginate(12)->withQueryString();
        $categorias = Categoria::orderBy('nombre')->pluck('nombre', 'id');

        return view('panel.productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        return view('panel.productos.form', $this->catalogos() + [
            'producto' => new Producto(['activo' => true, 'stock_minimo' => 5, 'unidad' => 'pieza', 'sku' => $this->siguienteSku()]),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos = $this->archivos($request, $datos);
        $producto = Producto::create($datos);
        $this->galeria($request, $producto);
        Bitacora::registrar('crear', 'productos', "Creó el producto {$producto->sku} {$producto->nombre}");

        return redirect()->route('panel.productos.show', $producto)->with('success', 'Producto creado correctamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'proveedor', 'imagenes']);
        $ventas = $producto->detalles()->whereHas('pedido', fn ($q) => $q->where('estado', '!=', 'cancelado'))
            ->selectRaw('COALESCE(SUM(cantidad),0) unidades, COALESCE(SUM(importe),0) importe')->first();

        return view('panel.productos.show', compact('producto', 'ventas'));
    }

    public function edit(Producto $producto)
    {
        $producto->load('imagenes');

        return view('panel.productos.form', $this->catalogos() + compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $this->validar($request, $producto);
        $datos = $this->archivos($request, $datos, $producto);
        $producto->update($datos);
        $this->galeria($request, $producto);
        Bitacora::registrar('editar', 'productos', "Editó el producto {$producto->sku}");

        return redirect()->route('panel.productos.show', $producto)->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        $ventas = $producto->detalles()->count();
        if ($ventas > 0) {
            return back()->with('error', "No se puede eliminar «{$producto->nombre}»: aparece en {$ventas} pedido(s). Desactívalo para ocultarlo del catálogo.");
        }

        foreach ($producto->imagenes as $img) {
            Archivos::eliminar($img->ruta);
        }
        Archivos::eliminar($producto->imagen);
        Archivos::eliminar($producto->ficha_tecnica);
        $producto->delete();
        Bitacora::registrar('eliminar', 'productos', "Eliminó el producto {$producto->sku}");

        return redirect()->route('panel.productos.index')->with('success', 'Producto eliminado.');
    }

    public function eliminarImagen(Producto $producto, ProductoImagen $imagen)
    {
        abort_unless($imagen->producto_id === $producto->id, 404);
        Archivos::eliminar($imagen->ruta);
        $imagen->delete();

        return back()->with('success', 'Imagen eliminada de la galería.');
    }

    /** Código QR (endroid/qr-code) que apunta a la ficha pública del producto. */
    public function qr(Producto $producto)
    {
        $svg = (new Builder(writer: new SvgWriter, data: route('catalogo').'?producto='.$producto->id, size: 260, margin: 8))
            ->build()->getString();

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }

    private function validar(Request $request, ?Producto $p = null): array
    {
        $request->merge(['sku' => strtoupper(trim((string) $request->sku))]);

        $datos = $request->validate([
            'sku' => ['required', 'regex:/^[A-Z0-9\-]{3,30}$/', Rule::unique('productos', 'sku')->ignore($p)],
            'nombre' => ['required', 'string', 'min:3', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'categoria_id' => ['required', Rule::exists('categorias', 'id')->where('activo', true)],
            'proveedor_id' => ['nullable', Rule::exists('proveedores', 'id')->where('activo', true)],
            'costo' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'precio' => ['required', 'numeric', 'gt:0', 'gte:costo', 'max:9999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'stock_minimo' => ['required', 'integer', 'min:0', 'max:100000'],
            'unidad' => ['required', 'in:pieza,caja,paquete,kg,litro,metro,servicio'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=300,min_height=300'],
            'imagenes' => ['nullable', 'array', 'max:'.self::MAX_GALERIA],
            'imagenes.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'ficha_tecnica' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'sku.regex' => 'El SKU solo admite letras mayúsculas, números y guiones (3 a 30 caracteres).',
            'precio.gte' => 'El precio de venta no puede ser menor que el costo.',
            'categoria_id.exists' => 'Selecciona una categoría activa.',
            'proveedor_id.exists' => 'Selecciona un proveedor activo.',
        ]);

        if ($p && $request->hasFile('imagenes') && $p->imagenes()->count() + count($request->file('imagenes')) > self::MAX_GALERIA) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'imagenes' => 'La galería admite máximo '.self::MAX_GALERIA.' imágenes. Elimina alguna antes de subir más.',
            ]);
        }

        $datos['destacado'] = $request->boolean('destacado');
        $datos['activo'] = $request->boolean('activo');
        unset($datos['imagen'], $datos['imagenes'], $datos['ficha_tecnica']);

        return $datos;
    }

    private function archivos(Request $request, array $datos, ?Producto $p = null): array
    {
        if ($request->hasFile('imagen')) {
            Archivos::eliminar($p?->imagen);
            $datos['imagen'] = Archivos::imagen($request->file('imagen'), 'productos', 1000);
        }
        if ($request->hasFile('ficha_tecnica')) {
            Archivos::eliminar($p?->ficha_tecnica);
            $datos['ficha_tecnica'] = Archivos::documento($request->file('ficha_tecnica'), 'documentos');
        }

        return $datos;
    }

    private function galeria(Request $request, Producto $producto): void
    {
        $orden = (int) $producto->imagenes()->max('orden');
        foreach ($request->file('imagenes', []) as $archivo) {
            $producto->imagenes()->create(['ruta' => Archivos::imagen($archivo, 'productos/galeria'), 'orden' => ++$orden]);
        }
    }

    private function catalogos(): array
    {
        return [
            'categorias' => Categoria::where('activo', true)->orderBy('nombre')->pluck('nombre', 'id'),
            'proveedores' => Proveedor::where('activo', true)->orderBy('razon_social')->pluck('razon_social', 'id'),
            'unidades' => ['pieza' => 'Pieza', 'caja' => 'Caja', 'paquete' => 'Paquete', 'kg' => 'Kilogramo', 'litro' => 'Litro', 'metro' => 'Metro', 'servicio' => 'Servicio'],
        ];
    }

    private function siguienteSku(): string
    {
        return 'CVZ-'.str_pad((string) ((int) Producto::max('id') + 1), 4, '0', STR_PAD_LEFT);
    }
}
