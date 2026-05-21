<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user());
        }

        return back()
            ->withErrors(['email' => 'Las credenciales no son correctas.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    private function redirectByRole($user)
    {
        // El modelo User usa role->name (no role->nombre)
        $rol = $user->role?->name ?? 'ciudadano';

        return match ($rol) {
            'autoridad_municipal'      => redirect()->route('panel.index'),
            'planificador_territorial' => redirect()->route('planificador.index'),
            'analista'                 => redirect()->route('analista.index'),
            default                    => redirect()->route('home'),
        };
    }
}
