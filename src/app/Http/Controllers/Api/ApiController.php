<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD genérico para la API pública (sin validación ni autenticación).
 *
 * Nota: "sin validación" significa que los datos se guardan tal como llegan
 * (solo se respetan los campos $fillable del modelo). Si la base de datos
 * rechaza algo (p. ej. una llave foránea inexistente) se responde un JSON
 * claro en lugar de una página de error.
 */
abstract class ApiController extends Controller
{
    /** @var class-string<Model> */
    protected string $modelo;

    protected string $recurso;

    /** "o" para masculino (producto, proveedor), "a" para femenino (categoría). */
    protected string $genero = 'o';

    protected array $relaciones = [];

    protected function consulta(Request $request)
    {
        return $this->modelo::query()->with($this->relaciones);
    }

    protected function transformar(Model $m): array
    {
        return $m->toArray();
    }

    public function index(Request $request): JsonResponse
    {
        $datos = $this->consulta($request)->get()->map(fn ($m) => $this->transformar($m));

        return response()->json(['data' => $datos, 'total' => $datos->count()]);
    }

    public function show(int $id): JsonResponse
    {
        $m = $this->modelo::with($this->relaciones)->find($id);

        return $m
            ? response()->json(['data' => $this->transformar($m)])
            : $this->noEncontrado($id);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->guardar(fn () => $this->modelo::create($request->all()), 201, 'cread');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $m = $this->modelo::find($id);
        if (! $m) {
            return $this->noEncontrado($id);
        }

        return $this->guardar(fn () => tap($m)->update($request->all()), 200, 'actualizad');
    }

    public function destroy(int $id): JsonResponse
    {
        $m = $this->modelo::find($id);
        if (! $m) {
            return $this->noEncontrado($id);
        }

        try {
            $m->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => "No se puede eliminar: el registro {$id} está relacionado con otros datos.",
            ], 409);
        }

        return response()->json(['message' => ucfirst($this->recurso)." {$id} eliminad{$this->genero} correctamente."]);
    }

    protected function guardar(callable $fn, int $status, string $verbo): JsonResponse
    {
        try {
            $m = $fn();
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'La base de datos rechazó la operación.',
                'detalle' => $e->errorInfo[2] ?? $e->getMessage(),
            ], 409);
        }

        return response()->json([
            'message' => ucfirst($this->recurso)." {$verbo}{$this->genero} correctamente.",
            'data' => $this->transformar($m->fresh($this->relaciones)),
        ], $status);
    }

    protected function noEncontrado(int $id): JsonResponse
    {
        return response()->json(['message' => ucfirst($this->recurso)." {$id} no encontrad{$this->genero}."], 404);
    }
}
