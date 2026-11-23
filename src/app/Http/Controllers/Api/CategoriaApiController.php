<?php

namespace App\Http\Controllers\Api;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CategoriaApiController extends ApiController
{
    protected string $modelo = Categoria::class;

    protected string $recurso = 'categoría';

    protected string $genero = 'a';

    protected function consulta(Request $request)
    {
        return Categoria::query()
            ->withCount(['productos' => fn ($q) => $q->where('activo', true)])
            ->when($request->boolean('activas'), fn ($q) => $q->where('activo', true))
            ->orderBy('nombre');
    }

    protected function transformar(Model $m): array
    {
        return [
            'id' => $m->id,
            'nombre' => $m->nombre,
            'slug' => $m->slug,
            'descripcion' => $m->descripcion,
            'color' => $m->color,
            'activo' => $m->activo,
            'productos_count' => $m->productos_count ?? $m->productos()->count(),
            'creado' => $m->created_at?->toDateTimeString(),
        ];
    }
}
