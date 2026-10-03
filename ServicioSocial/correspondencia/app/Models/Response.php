<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'sent_document_id',
        'user_id',
        'response_text',
        'response_date',
    ];

    protected function casts(): array
    {
        return [
            'response_date' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function sentDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'sent_document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
