<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case RECEIVED = 'received';
    case UNDER_REVIEW = 'under_review';
    case ASSIGNED = 'assigned';
    case IN_PROCESS = 'in_process';
    case IN_RESPONSE = 'in_response';
    case ATTENDED = 'attended';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';
    case RETURNED = 'returned';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::RECEIVED => 'Recibido',
            self::UNDER_REVIEW => 'En Revisión',
            self::ASSIGNED => 'Asignado',
            self::IN_PROCESS => 'En Atención',
            self::IN_RESPONSE => 'En Respuesta',
            self::ATTENDED => 'Atendido',
            self::CLOSED => 'Cerrado',
            self::ARCHIVED => 'Archivado',
            self::CANCELLED => 'Cancelado',
            self::REJECTED => 'Rechazado',
            self::RETURNED => 'Devuelto',
            self::EXPIRED => 'Vencido',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::RECEIVED => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            self::UNDER_REVIEW => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            self::ASSIGNED => 'bg-primary-subtle text-primary border border-primary-subtle',
            self::IN_PROCESS => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::IN_RESPONSE => 'bg-info text-dark border border-info',
            self::ATTENDED => 'bg-success-subtle text-success border border-success-subtle',
            self::CLOSED => 'bg-dark-subtle text-dark border border-dark-subtle',
            self::ARCHIVED => 'bg-secondary text-white',
            self::CANCELLED => 'bg-danger-subtle text-danger border border-danger-subtle',
            self::REJECTED => 'bg-danger text-white',
            self::RETURNED => 'bg-warning text-dark',
            self::EXPIRED => 'bg-danger text-white fw-bold',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::RECEIVED => 'bi-inbox',
            self::UNDER_REVIEW => 'bi-search',
            self::ASSIGNED => 'bi-person-check',
            self::IN_PROCESS => 'bi-gear-wide-connected',
            self::IN_RESPONSE => 'bi-chat-left-text',
            self::ATTENDED => 'bi-check-circle',
            self::CLOSED => 'bi-lock',
            self::ARCHIVED => 'bi-archive',
            self::CANCELLED => 'bi-x-circle',
            self::REJECTED => 'bi-hand-thumbs-down',
            self::RETURNED => 'bi-arrow-return-left',
            self::EXPIRED => 'bi-alarm',
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::RECEIVED => [self::UNDER_REVIEW, self::ASSIGNED, self::REJECTED, self::CANCELLED],
            self::UNDER_REVIEW => [self::ASSIGNED, self::IN_PROCESS, self::RETURNED, self::REJECTED],
            self::ASSIGNED => [self::IN_PROCESS, self::UNDER_REVIEW, self::RETURNED],
            self::IN_PROCESS => [self::IN_RESPONSE, self::ATTENDED, self::RETURNED, self::EXPIRED],
            self::IN_RESPONSE => [self::ATTENDED, self::IN_PROCESS, self::EXPIRED],
            self::ATTENDED => [self::CLOSED, self::IN_PROCESS],
            self::CLOSED => [self::ARCHIVED, self::ATTENDED],
            self::ARCHIVED => [self::CLOSED],
            self::RETURNED => [self::UNDER_REVIEW, self::ASSIGNED, self::CANCELLED],
            self::EXPIRED => [self::IN_PROCESS, self::ATTENDED, self::CLOSED],
            self::CANCELLED, self::REJECTED => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }
}
