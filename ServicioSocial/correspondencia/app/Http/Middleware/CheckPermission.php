<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->isActive()) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Su cuenta está inactiva. Contacte al administrador.']);
        }

        if ($user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'No tiene los permisos necesarios para realizar esta acción.');
    }
}
