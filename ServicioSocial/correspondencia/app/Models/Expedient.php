<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Expedient extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'description',
        'department_id',
        'responsible_user_id',
        'status',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'date',
            'closed_at' => 'date',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'expedient_documents')
            ->withPivot('added_by', 'created_at')
            ->orderBy('documents.received_or_sent_at', 'asc');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForUser($query, User $user)
    {
        if ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria'])) {
            return $query;
        }

        return $query->where('department_id', $user->department_id);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Abierto',
            'in_progress' => 'En Trámite',
            'closed' => 'Cerrado',
            'archived' => 'Archivado',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'open' => 'bg-success-subtle text-success border border-success-subtle',
            'in_progress' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'closed' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'archived' => 'bg-dark-subtle text-dark border border-dark-subtle',
            default => 'bg-light text-dark',
        };
    }
}
