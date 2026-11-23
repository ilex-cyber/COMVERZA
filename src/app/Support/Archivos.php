<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Carga de archivos de COMVERZA.
 *
 * Los archivos se guardan en public/uploads/{carpeta}. Las imágenes se
 * procesan con Intervention Image: se reducen a un máximo de 1200 px y se
 * convierten a WebP para que la página cargue rápido.
 */
class Archivos
{
    public static function imagen(UploadedFile $archivo, string $carpeta, int $max = 1200): string
    {
        $dir = public_path("uploads/{$carpeta}");
        File::ensureDirectoryExists($dir);

        // WebP si el servidor lo soporta; si no, JPG (p. ej. GD compilado sin libwebp)
        $nombre = Str::uuid().(function_exists('imagewebp') ? '.webp' : '.jpg');

        ImageManager::usingDriver(Driver::class)
            ->decodePath($archivo->getRealPath())
            ->scaleDown($max, $max)
            ->save("{$dir}/{$nombre}", quality: 82);

        return "uploads/{$carpeta}/{$nombre}";
    }

    public static function documento(UploadedFile $archivo, string $carpeta): string
    {
        $dir = public_path("uploads/{$carpeta}");
        File::ensureDirectoryExists($dir);

        $nombre = Str::uuid().'.'.strtolower($archivo->getClientOriginalExtension() ?: $archivo->extension());
        $archivo->move($dir, $nombre);

        return "uploads/{$carpeta}/{$nombre}";
    }

    /** Elimina un archivo subido (nunca borra imágenes base del sitio). */
    public static function eliminar(?string $ruta): void
    {
        if ($ruta && str_starts_with($ruta, 'uploads/') && File::exists(public_path($ruta))) {
            File::delete(public_path($ruta));
        }
    }
}
