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
    $credenciales = $request->validate([
        'usuario' => ['required', 'string'],
        'password' => ['required'],
    ], [
        'usuario.required' => 'Ingresa tu usuario.',
        'password.required' => 'Ingresa tu contraseña.',
    ]);

    // Solo pueden entrar usuarios activos
    $credenciales['activo'] = true;

    if (Auth::attempt($credenciales, $request->boolean('remember'))) {
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    return back()
        ->withErrors(['usuario' => 'Usuario o contraseña incorrectos, o el usuario está inactivo.'])
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
