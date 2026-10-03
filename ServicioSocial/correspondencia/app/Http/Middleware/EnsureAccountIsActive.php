<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->isActive()) {
                AuditService::log(
                    'logout',
                    "Sesión cancelada forzosamente por cuenta desactivada para el usuario [{$user->name}].",
                    $user,
                    null,
                    null,
                    $user
                );

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Su cuenta institucional ha sido desactivada por la administración.');
            }
        }

        return $next($request);
    }
}
