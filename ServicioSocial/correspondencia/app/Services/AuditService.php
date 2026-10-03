<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public function recordHistory(
        Document $document,
        ?User $user,
        string $action,
        string $description,
        mixed $oldStatus = null,
        mixed $newStatus = null,
        ?string $ipAddress = null
    ): DocumentHistory {
        $oldVal = $oldStatus instanceof DocumentStatus ? $oldStatus->value : $oldStatus;
        $newVal = $newStatus instanceof DocumentStatus ? $newStatus->value : $newStatus;

        return DocumentHistory::create([
            'document_id' => $document->id,
            'user_id' => $user ? $user->id : Auth::id(),
            'action' => $action,
            'description' => $description,
            'old_status' => $oldVal,
            'new_status' => $newVal,
            'ip_address' => $ipAddress ?: request()?->ip(),
            'created_at' => now(),
        ]);
    }

    public static function log(
        string $event,
        string $description,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $user = null
    ): AuditLog {
        $userId = $user ? $user->id : Auth::id();
        $ip = request()?->ip();
        $agent = request()?->userAgent();

        return AuditLog::create([
            'user_id' => $userId,
            'event' => $event,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->getKey(),
            'ip_address' => $ip,
            'user_agent' => $agent,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}
