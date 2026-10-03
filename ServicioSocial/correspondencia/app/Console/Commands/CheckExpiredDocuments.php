<?php

namespace App\Console\Commands;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Services\AuditService;
use Illuminate\Console\Command;

class CheckExpiredDocuments extends Command
{
    protected $signature = 'documents:check-expired';
    protected $description = 'Verifica documentos con fecha límite vencida y actualiza su estado a vencido.';

    public function handle(AuditService $auditService): int
    {
        $expiredDocs = Document::pending()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->where('status', '!=', DocumentStatus::EXPIRED->value)
            ->get();

        $count = 0;
        foreach ($expiredDocs as $doc) {
            $oldStatus = $doc->status;
            $doc->status = DocumentStatus::EXPIRED;
            $doc->save();

            $formattedDate = $doc->due_date ? $doc->due_date->format('d/m/Y') : 'desconocida';
            $auditService->recordHistory(
                document: $doc,
                user: null,
                action: 'VENCIMIENTO_AUTOMATICO',
                description: "Fecha límite ({$formattedDate}) superada. Estado actualizado automáticamente a Vencido por el sistema.",
                oldStatus: $oldStatus,
                newStatus: DocumentStatus::EXPIRED,
                ipAddress: '127.0.0.1'
            );

            $count++;
        }

        $this->info("Se han actualizado {$count} documentos al estado Vencido.");
        return self::SUCCESS;
    }
}
