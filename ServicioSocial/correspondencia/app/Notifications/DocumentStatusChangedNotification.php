<?php

namespace App\Notifications;

use App\Enums\DocumentStatus;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Document $document,
        protected DocumentStatus $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'status_changed',
            'title' => 'Cambio de Estado en Correspondencia',
            'message' => "El documento [{$this->document->folio}] ha cambiado al estado: {$this->newStatus->label()}",
            'document_id' => $this->document->id,
            'folio' => $this->document->folio,
            'new_status' => $this->newStatus->label(),
            'url' => route('documents.show', $this->document),
            'icon' => 'bi-arrow-repeat text-warning',
        ];
    }
}
