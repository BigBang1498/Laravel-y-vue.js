<?php

namespace App\Models;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Enums\Priority;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'direction',
        'folio',
        'external_number',
        'document_type_id',
        'sender_name',
        'sender_institution',
        'recipient_name',
        'recipient_institution',
        'subject',
        'description',
        'document_date',
        'received_or_sent_at',
        'department_id',
        'created_by',
        'current_assigned_user_id',
        'priority',
        'status',
        'due_date',
        'attended_at',
        'closed_at',
        'security_level',
        'observations',
    ];

    protected function casts(): array
    {
        return [
            'direction' => DocumentDirection::class,
            'status' => DocumentStatus::class,
            'priority' => Priority::class,
            'document_date' => 'date',
            'due_date' => 'date',
            'received_or_sent_at' => 'datetime',
            'attended_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_assigned_user_id');
    }

    public function assignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Assignment::class)->orderBy('id', 'desc');
    }

    public function currentAssignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Assignment::class)->where('is_current', true);
    }

    public function history(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DocumentHistory::class)->orderBy('id', 'desc');
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attachment::class)->orderBy('id', 'desc');
    }

    public function responses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Response::class)->orderBy('id', 'desc');
    }

    public function expedients(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Expedient::class, 'expedient_documents')
            ->withPivot('added_by', 'created_at');
    }

    public function relatedDocuments(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_relationships', 'document_id', 'related_document_id')
            ->withPivot('relation_type', 'created_at');
    }

    public function scopeReceived($query)
    {
        return $query->where('direction', DocumentDirection::RECEIVED->value);
    }

    public function scopeSent($query)
    {
        return $query->where('direction', DocumentDirection::SENT->value);
    }

    public function scopePending($query)
    {
        return $query->whereNotIn('status', [
            DocumentStatus::ATTENDED->value,
            DocumentStatus::CLOSED->value,
            DocumentStatus::ARCHIVED->value,
            DocumentStatus::CANCELLED->value,
            DocumentStatus::REJECTED->value,
        ]);
    }

    public function scopeForUser($query, User $user)
    {
        if ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria'])) {
            return $query;
        }

        if ($user->hasRole('jefe_departamento')) {
            return $query->where('department_id', $user->department_id);
        }

        if ($user->hasRole('empleado')) {
            return $query->where('current_assigned_user_id', $user->id)
                ->orWhere('department_id', $user->department_id);
        }

        return $query;
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        $now = Carbon::today();
        return (int) $now->diffInDays($this->due_date, false);
    }

    public function getTrafficLightAttribute(): string
    {
        if (in_array($this->status, [DocumentStatus::ATTENDED, DocumentStatus::CLOSED, DocumentStatus::ARCHIVED, DocumentStatus::CANCELLED, DocumentStatus::REJECTED])) {
            return 'gray';
        }

        if (!$this->due_date) {
            return 'green';
        }

        $days = $this->days_remaining;

        if ($days < 0 || $this->status === DocumentStatus::EXPIRED) {
            return 'red';
        }

        if ($days <= 3) {
            return 'yellow';
        }

        return 'green';
    }

    public function canTransitionTo(DocumentStatus $target): bool
    {
        return $this->status->canTransitionTo($target);
    }
}
