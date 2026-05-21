<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $this->redirectByRole(Auth::guard($guard)->user());
            }
        }

        return $next($request);
    }

    private function redirectByRole($user): \Illuminate\Http\RedirectResponse
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
