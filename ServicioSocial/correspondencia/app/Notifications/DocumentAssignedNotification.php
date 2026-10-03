<?php

namespace App\Notifications;

use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Document $document,
        protected User $assignedBy
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'document_assigned',
            'title' => 'Nuevo Documento Asignado',
            'message' => "Se le ha turnado el documento [{$this->document->folio}]: {$this->document->subject}",
            'document_id' => $this->document->id,
            'folio' => $this->document->folio,
            'assigned_by' => $this->assignedBy->name,
            'due_date' => $this->document->due_date?->format('d/m/Y'),
            'url' => route('documents.show', $this->document),
            'icon' => 'bi-person-check-fill text-primary',
        ];
    }
}
