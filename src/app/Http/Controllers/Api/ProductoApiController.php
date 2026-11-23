<?php

namespace App\Http\Controllers\Api;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ProductoApiController extends ApiController
{
    protected string $modelo = Producto::class;

    protected string $recurso = 'producto';

    protected array $relaciones = ['categoria:id,nombre,color', 'proveedor:id,razon_social,ciudad'];

    /** Filtros opcionales: ?categoria=ID&destacados=1&buscar=texto&activos=1&limite=8 */
    protected function consulta(Request $request)
    {
        return Producto::query()->with($this->relaciones)
            ->when($request->filled('categoria'), fn ($q) => $q->where('categoria_id', $request->integer('categoria')))
            ->when($request->boolean('destacados'), fn ($q) => $q->where('destacado', true))
            ->when($request->boolean('activos'), fn ($q) => $q->where('activo', true))
            ->when($request->filled('buscar'), fn ($q) => $q->where(fn ($w) => $w
                ->where('nombre', 'like', '%'.$request->buscar.'%')
                ->orWhere('sku', 'like', '%'.$request->buscar.'%')))
            ->when($request->filled('limite'), fn ($q) => $q->limit($request->integer('limite')))
            ->orderByDesc('destacado')->orderBy('nombre');
    }

    protected function transformar(Model $m): array
    {
        return [
            'id' => $m->id,
            'sku' => $m->sku,
            'nombre' => $m->nombre,
            'descripcion' => $m->descripcion,
            'precio' => (float) $m->precio,
            'stock' => $m->stock,
            'disponible' => $m->stock > 0,
            'unidad' => $m->unidad,
            'imagen' => $m->imagen_url,
            'destacado' => $m->destacado,
            'activo' => $m->activo,
            'categoria_id' => $m->categoria_id,
            'categoria' => $m->categoria?->only(['id', 'nombre', 'color']),
            'proveedor_id' => $m->proveedor_id,
            'proveedor' => $m->proveedor?->only(['id', 'razon_social', 'ciudad']),
        ];
    }
}
