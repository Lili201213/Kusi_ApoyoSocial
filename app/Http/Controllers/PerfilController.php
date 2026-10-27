<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function edit()
    {
        return view('perfil.edit');
    }

    /** Datos personales (el usuario y el rol solo los cambia el administrador) */
    public function updateDatos(Request $request)
    {
        $u = $request->user();

        $datos = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($u->id)],
            'telefono' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Tu nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio: se usa para recuperar tu contraseña.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Ese correo ya está registrado por otro usuario.',
        ]);

        $cambiaCorreo = $u->email !== $datos['email'];
        $falloCorreo = false;

        try {
            // Si cambia el correo, el modelo lo marca como no verificado y envía el enlace
            $u->update($datos);
        } catch (\Throwable $e) {
            // Si solo falló el envío del correo, los datos ya quedaron guardados
            if (! $cambiaCorreo || $u->fresh()->email !== $datos['email']) {
                throw $e;
            }
            report($e);
            $falloCorreo = true;
        }

        // Con correo nuevo debe verificarlo: se le lleva directo a la pantalla de verificación
        if ($cambiaCorreo) {
            $respuesta = redirect()->route('verification.notice');

            return $falloCorreo
                ? $respuesta->with('error', 'Se guardó tu nuevo correo, pero no se pudo enviar el enlace de verificación. Pulsa "Reenviar enlace".')
                : $respuesta->with('status', 'enlace-enviado');
        }

        return redirect()->route('perfil.edit')->with('exito', 'Tus datos fueron actualizados.');
    }

    public function updateClave(Request $request)
    {
        $request->validate([
            'password_actual' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:password_actual', Password::min(8)->letters()->numbers()],
        ], [
            'password_actual.required' => 'Ingresa tu contraseña actual.',
            'password_actual.current_password' => 'La contraseña actual no es correcta.',
            'password.required' => 'Ingresa la nueva contraseña.',
            'password.confirmed' => 'La confirmación no coincide.',
            'password.different' => 'La nueva contraseña debe ser distinta a la actual.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe incluir letras.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
        ]);

        $request->user()->update(['password' => $request->password]);

        return redirect()->route('perfil.edit')->with('exito', 'Contraseña actualizada correctamente.');
    }

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'foto.required' => 'Selecciona una imagen.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser JPG, PNG o WEBP.',
            'foto.max' => 'La foto no puede pesar más de 2 MB.',
        ]);

        $u = $request->user();
        $carpeta = public_path('uploads/perfiles');
        File::ensureDirectoryExists($carpeta);

        $archivo = $request->file('foto');
        $nombre = 'u'.$u->id.'_'.time().'.'.$archivo->extension();
        $archivo->move($carpeta, $nombre);

        $this->borrarArchivo($u);
        $u->update(['foto' => $nombre]);

        return redirect()->route('perfil.edit')->with('exito', 'Tu foto fue actualizada.');
    }

    public function deleteFoto(Request $request)
    {
        $u = $request->user();
        $this->borrarArchivo($u);
        $u->update(['foto' => null]);

        return redirect()->route('perfil.edit')->with('exito', 'Foto eliminada.');
    }

    /** Eliminar la propia cuenta (solo Personal y Supervisor) */
    public function destroy(Request $request)
    {
        $u = $request->user();

        // Protección en el servidor: aunque alguien fuerce la ruta, el admin no puede
        abort_unless($u->puedeEliminarCuenta(), 403, 'El administrador no puede eliminar su cuenta.');

        $request->validate([
            'password_eliminar' => ['required', 'current_password'],
        ], [
            'password_eliminar.required' => 'Ingresa tu contraseña para confirmar.',
            'password_eliminar.current_password' => 'La contraseña no es correcta.',
        ]);

        $this->borrarArchivo($u);
        $u->update(['foto' => null, 'activo' => false]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $u->delete(); // borrado lógico: se conservan sus registros históricos

        return redirect()->route('login')->with('exito', 'Tu cuenta fue eliminada correctamente.');
    }

    private function borrarArchivo(User $u): void
    {
        if ($u->foto) {
            $ruta = public_path('uploads/perfiles/'.$u->foto);
            if (is_file($ruta)) {
                @unlink($ruta);
            }
        }
    }
}
