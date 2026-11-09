<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistroController extends Controller
{
    /** El administrador nunca se puede elegir en el registro público */
    private const ROL_ADMIN = 1;

    /** true = la cuenta queda inactiva hasta que un administrador la active */
    private const REQUIERE_APROBACION = false;

    public function create()
    {
        return view('auth.register', [
            'roles' => $this->rolesPermitidos(),
        ]);
    }

    public function store(Request $request)
    {
        $idsPermitidos = $this->rolesPermitidos()->pluck('id')->all();

        if (empty($idsPermitidos)) {
            return back()->withInput()
                ->with('error', 'No se pudo crear la cuenta: no hay roles disponibles en el sistema. Avisa al administrador.');
        }

        $datos = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'usuario' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'usuario')],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'telefono' => ['nullable', 'string', 'max:20'],
            'rol_id' => ['required', Rule::in($idsPermitidos)],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.required' => 'Ingresa tu nombre completo.',
            'usuario.required' => 'Elige un nombre de usuario.',
            'usuario.min' => 'El usuario debe tener al menos 3 caracteres.',
            'usuario.regex' => 'El usuario solo puede tener letras, números, punto, guion y guion bajo (sin espacios).',
            'usuario.unique' => 'Ese nombre de usuario ya está en uso.',
            'email.required' => 'El correo es obligatorio: lo usaremos para verificar tu cuenta.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Ese correo ya está registrado.',
            'rol_id.required' => 'Elige el tipo de cuenta.',
            'rol_id.in' => 'El tipo de cuenta elegido no es válido.',
            'password.required' => 'Crea una contraseña.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe incluir letras.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
        ]);

        $usuario = User::create([
            'name' => $datos['name'],
            'usuario' => $datos['usuario'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'] ?? null,
            'password' => $datos['password'], // se guarda con hash por el cast del modelo
            'rol_id' => (int) $datos['rol_id'],
            'activo' => ! self::REQUIERE_APROBACION,
        ]);

        // Enlace para verificar el correo
        $falloCorreo = false;
        try {
            $usuario->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            report($e);
            $falloCorreo = true;
        }

        // Si hay que esperar aprobación, no inicia sesión todavía
        if (self::REQUIERE_APROBACION) {
            return redirect()->route('login')
                ->with('exito', 'Tu cuenta fue creada. Un administrador debe activarla antes de que puedas ingresar.');
        }

        Auth::login($usuario);
        $request->session()->regenerate();

        $respuesta = redirect()->route('verification.notice');

        return $falloCorreo
            ? $respuesta->with('error', 'Tu cuenta fue creada, pero no se pudo enviar el enlace de verificación. Pulsa "Reenviar enlace".')
            : $respuesta->with('status', 'enlace-enviado');
    }

    /** Todos los roles menos el Administrador */
    private function rolesPermitidos()
    {
        return Rol::where('id', '!=', self::ROL_ADMIN)->orderBy('id')->get();
    }
}
