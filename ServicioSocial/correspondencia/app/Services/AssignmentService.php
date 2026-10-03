<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Models\Assignment;
use App\Models\Department;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function assign(
        Document $document,
        User $assignedBy,
        int $departmentId,
        ?int $assignedToUserId = null,
        Carbon|string|null $dueDate = null,
        ?string $instructions = null,
        ?string $ipAddress = null
    ): Assignment {
        return DB::transaction(function () use ($document, $assignedBy, $departmentId, $assignedToUserId, $dueDate, $instructions, $ipAddress) {
            $isReassignment = $document->assignments()->where('is_current', true)->exists();
            $document->assignments()->where('is_current', true)->update(['is_current' => false]);

            $parsedDueDate = $dueDate ? Carbon::parse($dueDate) : null;

            $assignment = Assignment::create([
                'document_id' => $document->id,
                'assigned_by' => $assignedBy->id,
                'assigned_to_user_id' => $assignedToUserId,
                'assigned_to_department_id' => $departmentId,
                'assigned_at' => now(),
                'due_date' => $parsedDueDate,
                'instructions' => $instructions,
                'is_current' => true,
            ]);

            $document->department_id = $departmentId;
            $document->current_assigned_user_id = $assignedToUserId;
            if ($parsedDueDate) {
                $document->due_date = $parsedDueDate;
            }

            $oldStatus = $document->status;
            if (in_array($document->status, [DocumentStatus::RECEIVED, DocumentStatus::UNDER_REVIEW])) {
                $document->status = DocumentStatus::ASSIGNED;
            }

            $document->save();

            $dept = Department::find($departmentId);
            $deptName = $dept ? $dept->name : "Dept #{$departmentId}";
            $assignedUser = $assignedToUserId ? User::find($assignedToUserId) : null;
            $assignedUserName = $assignedUser ? $assignedUser->name : 'Área General';

            $action = $isReassignment ? 'REASIGNACION' : 'ASIGNACION';
            $description = "Documento turnado al departamento [{$deptName}] - Responsable: [{$assignedUserName}].";
            if ($parsedDueDate) {
                $formattedDue = $parsedDueDate->format('d/m/Y');
                $description .= " Fecha límite establecida: {$formattedDue}.";
            }
            if ($instructions) {
                $description .= " Instrucciones: \"{$instructions}\"";
            }

            $this->auditService->recordHistory(
                document: $document,
                user: $assignedBy,
                action: $action,
                description: $description,
                oldStatus: $oldStatus,
                newStatus: $document->status,
                ipAddress: $ipAddress
            );

            if ($assignedToUserId && $assignedToUserId !== $assignedBy->id) {
                $targetUser = User::find($assignedToUserId);
                $targetUser?->notify(new \App\Notifications\DocumentAssignedNotification($document, $assignedBy));
            }

            return $assignment;
        });
    }
}
