<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SesionController extends Controller
{
    public function mostrarFormulario(): View
    {
        return view('admin.auth.iniciar-sesion');
    }

    public function iniciarSesion(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credenciales, $request->boolean('recordar'))) {
            return back()->withErrors(['email' => 'Email o contraseña incorrectos.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.entradas.index'));
    }

    public function cerrarSesion(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
