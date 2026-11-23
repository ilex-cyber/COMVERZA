<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mail\ComverzaMail;
use App\Models\Bitacora;
use App\Models\User;
use App\Support\Archivos;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as ReglaPassword;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller implements HasMiddleware
{
    use ConPermisos;

    public static function middleware(): array
    {
        return self::permisosDe('usuarios');
    }

    public function index(Request $request)
    {
        $usuarios = User::with('roles')
            ->when($request->buscar, fn ($q, $b) => $q->where(fn ($w) => $w->where('name', 'like', "%{$b}%")->orWhere('email', 'like', "%{$b}%")))
            ->when($request->rol, fn ($q, $r) => $q->role($r))
            ->orderBy('name')->paginate(12)->withQueryString();

        return view('panel.usuarios.index', ['usuarios' => $usuarios, 'roles' => Role::orderBy('name')->pluck('name', 'name')]);
    }

    public function create()
    {
        return view('panel.usuarios.form', ['usuario' => new User(['activo' => true]), 'roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $generada = empty($datos['password']);
        $passwordPlano = $generada ? Str::password(12) : $datos['password'];
        $datos['password'] = $passwordPlano;
        if ($request->hasFile('avatar')) {
            $datos['avatar'] = Archivos::imagen($request->file('avatar'), 'avatares', 400);
        }

        $usuario = User::create($datos + ['email_verified_at' => now()]);
        $usuario->syncRoles($request->input('roles', []));
        Bitacora::registrar('crear', 'usuarios', "Creó al usuario {$usuario->email} con rol(es): ".implode(', ', $request->input('roles', [])));

        // Correo de bienvenida con enlace para que el usuario defina su propia contraseña
        $token = Password::createToken($usuario);
        Mail::to($usuario->email)->send(new ComverzaMail(
            asunto: 'Bienvenido a COMVERZA',
            titulo: "¡Bienvenido, {$usuario->name}!",
            intro: 'Se creó tu cuenta en la plataforma COMVERZA. Por seguridad, define tu propia contraseña con el botón de abajo.',
            filas: [['Usuario', $usuario->email], ['Rol', $usuario->getRoleNames()->implode(', ') ?: 'Sin rol']],
            botonTexto: 'Definir mi contraseña',
            botonUrl: route('password.reset', ['token' => $token, 'email' => $usuario->email]),
        ));

        return redirect()->route('panel.usuarios.index')->with('success', "Usuario creado. Se envió un correo de bienvenida a {$usuario->email}.");
    }

    public function edit(User $usuario)
    {
        return view('panel.usuarios.form', ['usuario' => $usuario, 'roles' => Role::orderBy('name')->get()]);
    }

    public function update(Request $request, User $usuario)
    {
        $datos = $this->validar($request, $usuario);
        $esYo = $usuario->is(auth()->user());

        if ($esYo && ! $datos['activo']) {
            return back()->withInput()->with('error', 'No puedes desactivar tu propia cuenta.');
        }
        if ($esYo && $usuario->hasRole('Administrador') && ! in_array('Administrador', $request->input('roles', []))) {
            return back()->withInput()->with('error', 'No puedes quitarte el rol de Administrador a ti mismo.');
        }
        if ($this->esUltimoAdmin($usuario) && (! $datos['activo'] || ! in_array('Administrador', $request->input('roles', [])))) {
            return back()->withInput()->with('error', 'Debe existir al menos un Administrador activo en el sistema.');
        }

        if (empty($datos['password'])) {
            unset($datos['password']);
        }
        if ($request->hasFile('avatar')) {
            Archivos::eliminar($usuario->avatar);
            $datos['avatar'] = Archivos::imagen($request->file('avatar'), 'avatares', 400);
        }
        $usuario->update($datos);
        $usuario->syncRoles($request->input('roles', []));
        Bitacora::registrar('editar', 'usuarios', "Editó al usuario {$usuario->email}; roles: ".$usuario->getRoleNames()->implode(', '));

        return redirect()->route('panel.usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->is(auth()->user())) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        if ($this->esUltimoAdmin($usuario)) {
            return back()->with('error', 'No puedes eliminar al último Administrador del sistema.');
        }

        Archivos::eliminar($usuario->avatar);
        $usuario->delete();
        Bitacora::registrar('eliminar', 'usuarios', "Eliminó al usuario {$usuario->email}");

        return redirect()->route('panel.usuarios.index')->with('success', 'Usuario eliminado.');
    }

    private function esUltimoAdmin(User $u): bool
    {
        return $u->hasRole('Administrador') && User::role('Administrador')->where('activo', true)->count() <= 1;
    }

    private function validar(Request $request, ?User $u = null): array
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s\.]+$/u'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($u)],
            'telefono' => ['nullable', 'regex:/^\d{10}$/'],
            'puesto' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'confirmed', ReglaPassword::min(8)->letters()->mixedCase()->numbers()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [Rule::exists('roles', 'name')],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ], [
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos.',
            'roles.required' => 'Asigna al menos un rol al usuario.',
        ]);
        $datos['activo'] = $request->boolean('activo');
        unset($datos['roles'], $datos['avatar']);

        return $datos;
    }
}
