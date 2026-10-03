<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            AuditService::log('failed_login', "Bloqueo por exceso de intentos para el correo [{$request->email}].");
            throw ValidationException::withMessages([
                'email' => "Demasiados intentos de acceso. Por favor espere {$seconds} segundos.",
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->isActive()) {
            RateLimiter::hit($throttleKey);
            AuditService::log('failed_login', "Intento de acceso a cuenta desactivada para el correo [{$request->email}].", $user);
            throw ValidationException::withMessages([
                'email' => 'Su cuenta se encuentra desactivada. Comuníquese con el administrador.',
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            $authUser = Auth::user();
            AuditService::log('login', "Inicio de sesión exitoso del usuario [{$authUser->name}] con rol [{$authUser->roles->first()?->label}].", $authUser, null, null, $authUser);
            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey);
        AuditService::log('failed_login', "Credenciales incorrectas para el correo [{$request->email}].");

        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser) {
            AuditService::log('logout', "Cierre de sesión voluntario del usuario [{$authUser->name}].", $authUser, null, null, $authUser);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
