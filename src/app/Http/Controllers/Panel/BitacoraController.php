<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $registros = Bitacora::with('usuario:id,name')
            ->when($request->modulo, fn ($q, $m) => $q->where('modulo', $m))
            ->when($request->accion, fn ($q, $a) => $q->where('accion', $a))
            ->latest('created_at')->latest('id')->paginate(25)->withQueryString();

        return view('panel.bitacora', [
            'registros' => $registros,
            'modulos' => Bitacora::distinct()->orderBy('modulo')->pluck('modulo', 'modulo'),
            'acciones' => Bitacora::distinct()->orderBy('accion')->pluck('accion', 'accion'),
        ]);
    }
}
