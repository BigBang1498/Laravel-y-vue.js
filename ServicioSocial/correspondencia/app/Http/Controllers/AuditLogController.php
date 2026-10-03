<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso restringido a administradores.');
        }

        $events = [
            'login' => 'Inicio de Sesión',
            'logout' => 'Cierre de Sesión',
            'failed_login' => 'Acceso Fallido',
            'created' => 'Creación de Registro',
            'updated' => 'Actualización',
            'deleted' => 'Eliminación',
            'assigned' => 'Asignación / Turno',
            'status_changed' => 'Cambio de Estado',
            'exported_report' => 'Exportación de Reporte',
            'downloaded_attachment' => 'Descarga de Adjunto',
        ];

        $users = User::orderBy('name')->get();

        $logs = AuditLog::with('user')
            ->filter($request->all())
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('audit-logs.index', compact('logs', 'events', 'users'));
    }

    public function show(AuditLog $auditLog)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso restringido a administradores.');
        }

        $auditLog->load('user');

        return view('audit-logs.show', compact('auditLog'));
    }
}
