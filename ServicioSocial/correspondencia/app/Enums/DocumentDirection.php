<?php

namespace App\Enums;

enum DocumentDirection: string
{
    case RECEIVED = 'received';
    case SENT = 'sent';

    public function label(): string
    {
        return match ($this) {
            self::RECEIVED => 'Correspondencia Recibida (Entrada)',
            self::SENT => 'Correspondencia Enviada (Salida)',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::RECEIVED => 'bg-primary-subtle text-primary border border-primary-subtle',
            self::SENT => 'bg-success-subtle text-success border border-success-subtle',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::RECEIVED => 'bi-inbox-fill',
            self::SENT => 'bi-send-fill',
        };
    }
}
