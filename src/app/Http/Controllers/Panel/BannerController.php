<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Bitacora;
use App\Support\Archivos;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

/** Fotos de presentación (carrusel) de la landing pública. */
class BannerController extends Controller implements HasMiddleware
{
    use ConPermisos;

    public static function middleware(): array
    {
        return self::permisosDe('banners');
    }

    public function index()
    {
        return view('panel.banners.index', ['banners' => Banner::orderBy('orden')->get()]);
    }

    public function create()
    {
        return view('panel.banners.form', ['banner' => new Banner(['activo' => true, 'orden' => (int) Banner::max('orden') + 1])]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request, true);
        $datos['imagen'] = Archivos::imagen($request->file('imagen'), 'banners', 1800);
        $b = Banner::create($datos);
        Bitacora::registrar('crear', 'banners', "Agregó la foto «{$b->titulo}» a la landing");

        return redirect()->route('panel.banners.index')->with('success', 'Foto agregada a la landing.');
    }

    public function edit(Banner $banner)
    {
        return view('panel.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $datos = $this->validar($request, false);
        if ($request->hasFile('imagen')) {
            Archivos::eliminar($banner->imagen);
            $datos['imagen'] = Archivos::imagen($request->file('imagen'), 'banners', 1800);
        }
        if (! $datos['activo'] && $banner->activo && Banner::where('activo', true)->count() <= 1) {
            return back()->withInput()->with('error', 'La landing necesita al menos una foto activa.');
        }
        $banner->update($datos);
        Bitacora::registrar('editar', 'banners', "Editó la foto «{$banner->titulo}»");

        return redirect()->route('panel.banners.index')->with('success', 'Foto actualizada.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->activo && Banner::where('activo', true)->count() <= 1) {
            return back()->with('error', 'No puedes eliminar la única foto activa de la landing.');
        }
        Archivos::eliminar($banner->imagen);
        $banner->delete();
        Bitacora::registrar('eliminar', 'banners', "Eliminó la foto «{$banner->titulo}»");

        return redirect()->route('panel.banners.index')->with('success', 'Foto eliminada.');
    }

    private function validar(Request $request, bool $nuevo): array
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'min:3', 'max:120'],
            'subtitulo' => ['nullable', 'string', 'max:255'],
            'enlace' => ['nullable', 'url', 'max:255'],
            'orden' => ['required', 'integer', 'min:1', 'max:99'],
            'imagen' => [$nuevo ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=1000,min_height=500'],
        ], ['imagen.dimensions' => 'La foto debe medir al menos 1000 × 500 px para verse nítida en la landing.']);
        $datos['activo'] = $request->boolean('activo');
        unset($datos['imagen']);

        return $datos;
    }
}
