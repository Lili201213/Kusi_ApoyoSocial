<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class CuentaController extends Controller
{
    public function edit()
    {
        return view('cuenta.edit');
    }

    public function update(Request $request)
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

        return back()->with('exito', 'Contraseña actualizada correctamente.');
    }
}
