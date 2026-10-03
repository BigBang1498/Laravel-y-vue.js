<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'description',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function eventLabel(): string
    {
        return match ($this->event) {
            'login' => 'Inicio de Sesión',
            'logout' => 'Cierre de Sesión',
            'failed_login' => 'Acceso Fallido',
            'created' => 'Creación de Registro',
            'updated' => 'Actualización',
            'deleted' => 'Eliminación',
            'assigned' => 'Asignación / Turno',
            'status_changed' => 'Cambio de Estado',
            'exported_report' => 'Exportación de Reporte',
            'downloaded_attachment' => 'Descarga de Adjunto',
            'restored' => 'Restauración',
            default => ucfirst(str_replace('_', ' ', $this->event)),
        };
    }

    public function eventBadgeClass(): string
    {
        return match ($this->event) {
            'login' => 'bg-success-subtle text-success border border-success-subtle',
            'logout' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'failed_login' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'created' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'updated' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            'deleted' => 'bg-danger text-white',
            'assigned' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'status_changed' => 'bg-purple-subtle text-purple border border-purple-subtle',
            'exported_report' => 'bg-dark-subtle text-dark border border-dark-subtle',
            'downloaded_attachment' => 'bg-primary text-white',
            default => 'bg-light text-dark border',
        };
    }

    public function eventIcon(): string
    {
        return match ($this->event) {
            'login' => 'bi-box-arrow-in-right text-success',
            'logout' => 'bi-box-arrow-right text-secondary',
            'failed_login' => 'bi-shield-slash text-danger',
            'created' => 'bi-plus-circle text-primary',
            'updated' => 'bi-pencil-square text-info',
            'deleted' => 'bi-trash text-danger',
            'assigned' => 'bi-person-check text-warning',
            'status_changed' => 'bi-arrow-repeat text-dark',
            'exported_report' => 'bi-file-earmark-spreadsheet text-success',
            'downloaded_attachment' => 'bi-download text-primary',
            default => 'bi-activity text-secondary',
        };
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('auditable_type', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
