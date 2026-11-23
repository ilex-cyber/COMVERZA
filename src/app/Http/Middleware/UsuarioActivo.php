<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cierra la sesión de usuarios desactivados y registra el último acceso.
 */
class UsuarioActivo
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Tu cuenta está desactivada. Contacta al administrador.']);
        }

        if ($user && (! $user->ultimo_acceso || $user->ultimo_acceso->lt(now()->subMinutes(5)))) {
            $user->forceFill(['ultimo_acceso' => now()])->saveQuietly();
        }

        return $next($request);
    }
}
