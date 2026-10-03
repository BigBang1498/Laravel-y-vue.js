<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;

class Phase7Seeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $secretaria = User::where('email', 'secretaria@universidad.edu')->first();
        $jefeSistemas = User::where('email', 'jefe_sistemas@universidad.edu')->first();
        $sistemasDept = Department::where('code', 'SIS')->first();
        $doc = Document::first();

        if ($admin) {
            AuditLog::create([
                'user_id' => $admin->id,
                'event' => 'login',
                'auditable_type' => User::class,
                'auditable_id' => $admin->id,
                'ip_address' => '192.168.1.10',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/128.0.0.0',
                'description' => "Inicio de sesión exitoso del usuario [{$admin->name}] con rol [Administrador].",
                'created_at' => now()->subHours(8),
            ]);

            if ($sistemasDept) {
                AuditLog::create([
                    'user_id' => $admin->id,
                    'event' => 'updated',
                    'auditable_type' => Department::class,
                    'auditable_id' => $sistemasDept->id,
                    'ip_address' => '192.168.1.10',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/128.0.0.0',
                    'old_values' => ['phone' => 'Ext. 2000', 'email' => 'sistemas_old@universidad.edu'],
                    'new_values' => ['phone' => 'Ext. 2001', 'email' => 'sistemas@universidad.edu'],
                    'description' => "Actualización de datos de contacto del departamento [{$sistemasDept->name}].",
                    'created_at' => now()->subHours(6),
                ]);
            }

            AuditLog::create([
                'user_id' => $admin->id,
                'event' => 'exported_report',
                'auditable_type' => null,
                'auditable_id' => null,
                'ip_address' => '192.168.1.10',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/128.0.0.0',
                'description' => 'Exportación de reporte consolidado institucional en formato Excel/CSV con 15 registros.',
                'created_at' => now()->subHours(4),
            ]);
        }

        if ($secretaria && $doc) {
            AuditLog::create([
                'user_id' => $secretaria->id,
                'event' => 'assigned',
                'auditable_type' => Document::class,
                'auditable_id' => $doc->id,
                'ip_address' => '192.168.1.25',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/128.0.0.0',
                'old_values' => ['assigned_to' => null, 'status' => 'received'],
                'new_values' => ['assigned_to' => $jefeSistemas?->id, 'status' => 'assigned'],
                'description' => "Turno formal del documento [{$doc->folio}] para atención prioritaria.",
                'created_at' => now()->subHours(3),
            ]);
        }

        AuditLog::create([
            'user_id' => null,
            'event' => 'failed_login',
            'auditable_type' => null,
            'auditable_id' => null,
            'ip_address' => '200.68.14.92',
            'user_agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64)',
            'description' => 'Intento fallido de acceso con credenciales incorrectas para el correo [rectoria_test@universidad.edu].',
            'created_at' => now()->subHours(2),
        ]);
    }
}
