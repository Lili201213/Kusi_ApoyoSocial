<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    private const ROL_ADMIN = 1;

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));

        $usuarios = User::with('rol')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")->orWhere('usuario', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('rol_id'), fn ($x) => $x->where('rol_id', $request->rol_id))
            ->when(in_array($request->input('estado'), ['1', '0'], true), fn ($x) => $x->where('activo', (bool) $request->estado))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => Rol::orderBy('id')->get(),
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('usuarios.create', [
            'usuario' => new User(['activo' => true]),
            'roles' => Rol::orderBy('id')->get(),
            'esPropio' => false,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge(['activo' => $request->boolean('activo')]);
        $datos = $this->validar($request);

        User::create($datos); // la contraseña se guarda con hash por el cast del modelo

        return redirect()->route('usuarios.index')->with('exito', 'Usuario registrado correctamente.');
    }

    public function edit(Request $request, User $usuario)
    {
        return view('usuarios.edit', [
            'usuario' => $usuario,
            'roles' => Rol::orderBy('id')->get(),
            'esPropio' => $usuario->id === $request->user()->id,
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $esPropio = $usuario->id === $request->user()->id;

        // Nadie puede cambiarse a sí mismo el rol ni desactivarse (evita quedarse sin acceso)
        $request->merge($esPropio
            ? ['rol_id' => $usuario->rol_id, 'activo' => $usuario->activo]
            : ['activo' => $request->boolean('activo')]);

        $datos = $this->validar($request, $usuario->id);

        // Siempre debe quedar al menos un administrador activo
        $dejaDeSerAdminActivo = $this->esAdminActivo($usuario)
            && (! $datos['activo'] || (int) $datos['rol_id'] !== self::ROL_ADMIN);

        if ($dejaDeSerAdminActivo && $this->adminsActivos($usuario->id) === 0) {
            return back()->withInput()->with('error', 'No se puede: es el único administrador activo. Primero crea o activa otro administrador.');
        }

        if (empty($datos['password'])) {
            unset($datos['password']); // no cambiar la contraseña si se deja vacía
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario actualizado correctamente.');
    }

    /** Los usuarios no se eliminan: se activan o desactivan */
    public function cambiarEstado(Request $request, User $usuario)
    {
        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No puedes desactivar tu propio usuario.');
        }

        if ($usuario->activo && $this->esAdminActivo($usuario) && $this->adminsActivos($usuario->id) === 0) {
            return back()->with('error', 'No se puede desactivar: es el único administrador activo.');
        }

        $usuario->update(['activo' => ! $usuario->activo]);

        return back()->with('exito', $usuario->activo ? 'Usuario activado.' : 'Usuario desactivado. Ya no podrá ingresar.');
    }

    private function esAdminActivo(User $u): bool
    {
        return $u->rol_id === self::ROL_ADMIN && $u->activo;
    }

    private function adminsActivos(int $excluirId): int
    {
        return User::where('rol_id', self::ROL_ADMIN)->where('activo', true)->where('id', '!=', $excluirId)->count();
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'usuario' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'usuario')->ignore($id)],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($id)],
            'rol_id' => ['required', 'exists:roles,id'],
            'activo' => ['boolean'],
            'password' => [$id ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.min' => 'El usuario debe tener al menos 3 caracteres.',
            'usuario.regex' => 'El usuario solo puede tener letras, números, punto, guion y guion bajo (sin espacios).',
            'usuario.unique' => 'Ese nombre de usuario ya está en uso.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Ese correo ya está registrado.',
            'rol_id.required' => 'Selecciona un rol.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe incluir letras.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
        ]);
    }
}
