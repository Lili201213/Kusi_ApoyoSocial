<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as ReglaClave;
use Throwable;

class RecuperarClaveController extends Controller
{
    /** Paso 1: formulario "Olvidé mi contraseña" */
    public function solicitar()
    {
        return view('auth.olvide');
    }

    /** Paso 2: envía el enlace al correo registrado */
    public function enviar(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Ingresa tu correo.',
            'email.email' => 'El correo no tiene un formato válido.',
        ]);

        try {
            // Solo usuarios activos. El broker limita a 1 solicitud por minuto por correo.
            Password::sendResetLink(['email' => $request->email, 'activo' => true]);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'email' => 'No se pudo enviar el correo. Avisa al administrador para revisar la configuración de correo del sistema.',
            ]);
        }

        // Mensaje genérico: no revela si el correo existe o no (seguridad)
        return back()->with('estado',
            'Si el correo está registrado en KUSI, te enviamos un enlace para restablecer tu contraseña. '
            .'Revisa tu bandeja de entrada (y la carpeta de spam).');
    }

    /** Paso 3: formulario de nueva contraseña (se abre desde el enlace del correo) */
    public function formulario(Request $request, string $token)
    {
        return view('auth.restablecer', ['token' => $token, 'email' => $request->query('email')]);
    }

    /** Paso 4: guarda la nueva contraseña */
    public function actualizar(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', ReglaClave::min(8)->letters()->numbers()],
        ], [
            'email.required' => 'El correo es obligatorio.',
            'password.required' => 'Ingresa la nueva contraseña.',
            'password.confirmed' => 'La confirmación no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe incluir letras.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
        ]);

        $estado = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token') + ['activo' => true],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,                 // se guarda con hash por el cast del modelo
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($estado === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('exito', '¡Listo! Tu contraseña fue restablecida. Ya puedes ingresar.');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'El enlace no es válido o ya venció. Solicita uno nuevo.']);
    }
}
