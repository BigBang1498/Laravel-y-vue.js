<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class VerifyAttachmentsIntegrityCommand extends Command
{
    protected $signature = 'app:verify-attachments-integrity';
    protected $description = 'Verifica la integridad criptográfica SHA-256 de todos los archivos adjuntos almacenados';

    public function handle(): int
    {
        $this->info('=== VERIFICACIÓN DE INTEGRIDAD DE ARCHIVOS DIGITALES (SHA-256) ===');

        $attachments = Attachment::all();

        if ($attachments->isEmpty()) {
            $this->line('No existen archivos adjuntos registrados para verificar.');
            return Command::SUCCESS;
        }

        $intact = 0;
        $mismatched = 0;
        $missing = 0;

        foreach ($attachments as $attachment) {
            if (!Storage::disk('local')->exists($attachment->file_path)) {
                $this->error("[FALTA ARCHIVO] ID #{$attachment->id} - {$attachment->original_name} - Ruta no encontrada: {$attachment->file_path}");
                $missing++;
                continue;
            }

            $fullPath = Storage::disk('local')->path($attachment->file_path);
            $calculatedHash = hash_file('sha256', $fullPath);

            if ($calculatedHash !== $attachment->file_hash) {
                $this->error("[HASH DISCREPANTE] ID #{$attachment->id} - {$attachment->original_name}");
                $this->line("  Esperado: {$attachment->file_hash}");
                $this->line("  Calculado: {$calculatedHash}");
                $mismatched++;
            } else {
                $intact++;
            }
        }

        $this->table(
            ['Resultado', 'Cantidad'],
            [
                ['Archivos íntegros y verificados', $intact],
                ['Archivos con hash modificado / alterados', $mismatched],
                ['Archivos no encontrados en almacenamiento', $missing],
            ]
        );

        if ($mismatched > 0 || $missing > 0) {
            $this->error("Se detectaron inconsistencias de integridad física o criptográfica en los adjuntos.");
            return Command::FAILURE;
        }

        $this->info("Verificación concluida con éxito. 100% de los archivos conservan su integridad criptográfica.");
        return Command::SUCCESS;
    }
}
