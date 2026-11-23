<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'max:100'],
        ]);

        if (! Auth::attempt($credenciales, $request->boolean('recordar'))) {
            Bitacora::create([
                'accion' => 'login_fallido', 'modulo' => 'seguridad',
                'descripcion' => 'Intento fallido para '.$credenciales['email'], 'ip' => $request->ip(),
            ]);

            return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
        }

        if (! Auth::user()->activo) {
            Auth::logout();

            return back()->withErrors(['email' => 'Tu cuenta está desactivada. Contacta al administrador.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        Auth::user()->forceFill(['ultimo_acceso' => now()])->saveQuietly();
        Bitacora::registrar('login', 'seguridad', 'Inicio de sesión');

        $destino = Auth::user()->can('dashboard.ver') ? route('panel.dashboard') : route('panel.perfil');

        return redirect()->intended($destino)->with('success', '¡Bienvenido de nuevo, '.Auth::user()->name.'!');
    }

    public function logout(Request $request)
    {
        Bitacora::registrar('logout', 'seguridad', 'Cierre de sesión');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Cerraste sesión correctamente.');
    }
}
