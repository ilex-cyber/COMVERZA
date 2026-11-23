<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as ReglaPassword;

/**
 * Recuperación de contraseña en autoservicio:
 *  1. El usuario captura su correo.
 *  2. Se envía un enlace firmado con token (vigencia de 60 min).
 *  3. El usuario define una nueva contraseña segura.
 */
class PasswordController extends Controller
{
    public function solicitar()
    {
        return view('auth.olvide');
    }

    public function enviarEnlace(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'max:150']]);

        $estado = Password::sendResetLink($request->only('email'));

        if ($estado === Password::RESET_THROTTLED) {
            return back()->withErrors(['email' => __($estado)])->onlyInput('email');
        }

        // Mismo mensaje exista o no el correo: evita enumerar usuarios.
        return back()->with('success', __('passwords.sent'));
    }

    public function formulario(Request $request, string $token)
    {
        return view('auth.restablecer', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function restablecer(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', ReglaPassword::min(8)->letters()->mixedCase()->numbers()],
        ]);

        $estado = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($user));
                Bitacora::create([
                    'user_id' => $user->id, 'accion' => 'password_reset', 'modulo' => 'seguridad',
                    'descripcion' => 'Contraseña restablecida por autoservicio', 'ip' => request()->ip(),
                ]);
            }
        );

        return $estado === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($estado))
            : back()->withErrors(['email' => __($estado)])->onlyInput('email');
    }
}
