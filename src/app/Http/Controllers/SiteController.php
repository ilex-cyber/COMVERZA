<?php

namespace App\Http\Controllers;

use App\Mail\ComverzaMail;
use App\Models\MensajeContacto;
use App\Support\Archivos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    /** Catálogo público: se alimenta de la API GET /api/v1/productos y /api/v1/categorias. */
    public function catalogo()
    {
        return view('pages.catalogo');
    }

    public function contacto()
    {
        return view('pages.contacto');
    }

    public function enviarContacto(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s\.]+$/u'],
            'empresa' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'tipo' => ['required', 'in:Empresa,Proveedor,Otro'],
            'mensaje' => ['required', 'string', 'min:10', 'max:2000'],
            'adjunto' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,docx', 'max:5120'],
        ], ['nombre.regex' => 'El nombre solo puede contener letras y espacios.']);

        if ($request->hasFile('adjunto')) {
            $datos['adjunto'] = Archivos::documento($request->file('adjunto'), 'contacto');
        }
        $mensaje = MensajeContacto::create($datos);

        try {
            // Aviso interno al equipo comercial
            Mail::to(config('app.admin_email'))->send(new ComverzaMail(
                asunto: "Nuevo mensaje de {$mensaje->nombre}",
                titulo: 'Nuevo mensaje desde el sitio web',
                intro: $mensaje->mensaje,
                filas: [
                    ['Nombre', $mensaje->nombre], ['Empresa', $mensaje->empresa ?: '—'],
                    ['Correo', $mensaje->email], ['Tipo', $mensaje->tipo],
                    ['Adjunto', $mensaje->adjunto ? 'Sí' : 'No'],
                ],
                botonTexto: 'Ver en el panel',
                botonUrl: route('panel.mensajes.show', $mensaje),
                replyToEmail: $mensaje->email,
            ));
            // Acuse de recibo al remitente
            Mail::to($mensaje->email)->send(new ComverzaMail(
                asunto: 'Recibimos tu mensaje',
                titulo: "¡Gracias, {$mensaje->nombre}!",
                intro: 'Recibimos tu mensaje y un asesor de COMVERZA te responderá en menos de 24 horas hábiles.',
                filas: [['Tipo de consulta', $mensaje->tipo], ['Tu mensaje', \Illuminate\Support\Str::limit($mensaje->mensaje, 300)]],
                botonTexto: 'Conoce la plataforma',
                botonUrl: route('plataforma'),
            ));
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el correo de contacto: '.$e->getMessage());
        }

        return redirect()->route('contacto')
            ->with('success', 'Tu mensaje fue recibido. Te enviamos un acuse a tu correo.');
    }
}
