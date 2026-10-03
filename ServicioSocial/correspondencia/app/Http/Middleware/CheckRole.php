<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->isActive()) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Su cuenta está inactiva. Contacte al administrador.']);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(403, 'No tiene autorización para acceder a este recurso.');
    }
}
