<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentDueSoonNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Document $document
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'due_soon',
            'title' => 'Alerta de Término Próximo a Vencer',
            'message' => "El documento [{$this->document->folio}] vence el {$this->document->due_date?->format('d/m/Y')}. Le restan {$this->document->days_remaining} día(s).",
            'document_id' => $this->document->id,
            'folio' => $this->document->folio,
            'due_date' => $this->document->due_date?->format('d/m/Y'),
            'url' => route('documents.show', $this->document),
            'icon' => 'bi-alarm-fill text-danger',
        ];
    }
}
