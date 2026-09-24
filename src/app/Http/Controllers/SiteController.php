<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{
    public function servicios()
    {
        return view('pages.servicios');
    }

    public function plataforma()
    {
        return view('pages.plataforma');
    }

    public function contacto()
    {
        return view('pages.contacto');
    }

    public function enviarContacto(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'empresa' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'tipo' => ['required', 'in:Empresa,Proveedor,Otro'],
            'mensaje' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        Log::info('Nuevo mensaje de contacto COMVERZA', $validated);

        return redirect()
            ->route('contacto')
            ->with('success', 'Tu mensaje fue recibido correctamente.');
    }
}