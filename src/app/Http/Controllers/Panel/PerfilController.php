<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Support\Archivos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as ReglaPassword;

class PerfilController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user()->load('roles.permissions');

        return view('panel.perfil', ['user' => $user, 'permisos' => $user->getAllPermissions()->pluck('name')->sort()->values()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $datos = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s\.]+$/u'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'telefono' => ['nullable', 'regex:/^\d{10}$/'],
            'puesto' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ], ['telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos.', 'name.regex' => 'El nombre solo puede contener letras.']);

        unset($datos['avatar']);
        if ($request->hasFile('avatar')) {
            Archivos::eliminar($user->avatar);
            $datos['avatar'] = Archivos::imagen($request->file('avatar'), 'avatares', 400);
        }
        $user->update($datos);
        Bitacora::registrar('editar', 'perfil', 'Actualizó su perfil');

        return back()->with('success', 'Perfil actualizado.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'password_actual' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:password_actual', ReglaPassword::min(8)->letters()->mixedCase()->numbers()],
        ], ['password.different' => 'La nueva contraseña debe ser distinta de la actual.']);

        $request->user()->update(['password' => $request->password]);
        Bitacora::registrar('password', 'seguridad', 'Cambió su contraseña desde el perfil');

        return back()->with('success', 'Contraseña actualizada.');
    }
}
