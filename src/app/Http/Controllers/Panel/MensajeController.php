<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\MensajeContacto;
use App\Support\Archivos;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function index(Request $request)
    {
        $mensajes = MensajeContacto::query()
            ->when($request->tipo, fn ($q, $t) => $q->where('tipo', $t))
            ->when($request->buscar, fn ($q, $b) => $q->where(fn ($w) => $w->where('nombre', 'like', "%{$b}%")->orWhere('email', 'like', "%{$b}%")))
            ->latest()->paginate(12)->withQueryString();

        return view('panel.mensajes.index', compact('mensajes'));
    }

    public function show(MensajeContacto $mensaje)
    {
        $mensaje->update(['leido' => true]);

        return view('panel.mensajes.show', compact('mensaje'));
    }

    public function destroy(MensajeContacto $mensaje)
    {
        Archivos::eliminar($mensaje->adjunto);
        $mensaje->delete();
        Bitacora::registrar('eliminar', 'mensajes', "Eliminó el mensaje de {$mensaje->email}");

        return redirect()->route('panel.mensajes.index')->with('success', 'Mensaje eliminado.');
    }
}
