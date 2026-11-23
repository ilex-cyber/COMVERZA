<?php

namespace App\Http\Controllers\Api;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ProveedorApiController extends ApiController
{
    protected string $modelo = Proveedor::class;

    protected string $recurso = 'proveedor';

    protected function consulta(Request $request)
    {
        return Proveedor::query()
            ->withCount('productos')
            ->when($request->boolean('activos'), fn ($q) => $q->where('activo', true))
            ->orderByDesc('calificacion')->orderBy('razon_social');
    }

    protected function transformar(Model $m): array
    {
        return [
            'id' => $m->id,
            'razon_social' => $m->razon_social,
            'rfc' => $m->rfc,
            'contacto' => $m->contacto,
            'email' => $m->email,
            'telefono' => $m->telefono,
            'ciudad' => $m->ciudad,
            'sitio_web' => $m->sitio_web,
            'logo' => $m->logo_url,
            'calificacion' => $m->calificacion,
            'activo' => $m->activo,
            'productos_count' => $m->productos_count ?? $m->productos()->count(),
        ];
    }
}
