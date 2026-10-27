<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'usuario' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'usuario.required' => 'Ingresa tu usuario o correo electrónico.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        $identificador = trim($request->input('usuario'));

        // Si parece un correo, busca por email; si no, por nombre de usuario
        $campo = filter_var($identificador, FILTER_VALIDATE_EMAIL) ? 'email' : 'usuario';

        $credenciales = [
            $campo => $identificador,
            'password' => $request->input('password'),
            'activo' => true, // solo pueden entrar usuarios activos
        ];

        if (Auth::attempt($credenciales, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['usuario' => 'Usuario/correo o contraseña incorrectos, o el usuario está inactivo.'])
            ->onlyInput('usuario');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
