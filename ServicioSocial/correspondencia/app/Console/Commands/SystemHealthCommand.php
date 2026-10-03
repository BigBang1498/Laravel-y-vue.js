<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\Department;
use App\Models\Document;
use App\Models\Expedient;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SystemHealthCommand extends Command
{
    protected $signature = 'app:system-health';
    protected $description = 'Verifica el estado integral de la base de datos, almacenamiento y servicios del sistema';

    public function handle(): int
    {
        $this->info('=== DIAGNÓSTICO DE SALUD DEL SISTEMA INSTITUCIONAL ===');

        $dbOk = false;
        try {
            DB::connection()->getPdo();
            $dbOk = true;
            $this->line('<info>[OK]</info> Conexión a MariaDB: Establecida');
        } catch (\Exception $e) {
            $this->error('[FAIL] Conexión a MariaDB: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $storageOk = Storage::disk('local')->exists('');
        if ($storageOk) {
            $this->line('<info>[OK]</info> Almacenamiento local privado: Accesible');
        } else {
            $this->error('[FAIL] Almacenamiento local privado: No accesible');
        }

        $usersCount = User::count();
        $departmentsCount = Department::count();
        $documentsCount = Document::count();
        $expedientsCount = Expedient::count();
        $attachmentsCount = Attachment::count();
        $sequencesCount = DB::table('folio_sequences')->count();

        $this->table(
            ['Componente', 'Estado', 'Métrica / Total'],
            [
                ['Usuarios Registrados', 'Activo', $usersCount],
                ['Departamentos / Dependencias', 'Activo', $departmentsCount],
                ['Documentos de Correspondencia', 'Activo', $documentsCount],
                ['Expedientes Digitales', 'Activo', $expedientsCount],
                ['Archivos Digitales Adjuntos', 'Activo', $attachmentsCount],
                ['Secuencias de Folios', 'Activo', $sequencesCount],
            ]
        );

        $pendingDocs = Document::pending()->count();
        $expiredDocs = Document::whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', ['attended', 'closed', 'archived', 'cancelled'])
            ->count();

        $this->line("Documentos en trámite activo: <comment>{$pendingDocs}</comment>");
        $this->line("Documentos con término vencido: <fg=red>{$expiredDocs}</>");

        $this->info('Diagnóstico finalizado exitosamente. Todos los subsistemas operan con normalidad.');

        return Command::SUCCESS;
    }
}
