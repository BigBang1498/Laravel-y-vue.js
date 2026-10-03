<?php

namespace App\Enums;

enum Priority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Baja',
            self::NORMAL => 'Normal',
            self::HIGH => 'Alta',
            self::URGENT => 'Urgente',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::LOW => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            self::NORMAL => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            self::HIGH => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::URGENT => 'bg-danger-subtle text-danger border border-danger-subtle fw-bold',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::LOW => 'bi-arrow-down-circle',
            self::NORMAL => 'bi-dash-circle',
            self::HIGH => 'bi-arrow-up-circle',
            self::URGENT => 'bi-exclamation-octagon-fill',
        };
    }

    public function weight(): int
    {
        return match ($this) {
            self::URGENT => 4,
            self::HIGH => 3,
            self::NORMAL => 2,
            self::LOW => 1,
        };
    }
}
