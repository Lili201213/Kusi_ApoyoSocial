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
            'password_actual.required' => __('Ingresa tu contraseña actual.'),
            'password_actual.current_password' => __('La contraseña actual no es correcta.'),
            'password.required' => __('Ingresa la nueva contraseña.'),
            'password.confirmed' => __('La confirmación no coincide.'),
            'password.different' => __('La nueva contraseña debe ser distinta a la actual.'),
            'password.min' => __('La contraseña debe tener al menos 8 caracteres.'),
            'password.letters' => __('La contraseña debe incluir letras.'),
            'password.numbers' => __('La contraseña debe incluir al menos un número.'),
        ]);

        $request->user()->update(['password' => $request->password]);

        return back()->with('exito', __('Contraseña actualizada correctamente.'));
    }
}
