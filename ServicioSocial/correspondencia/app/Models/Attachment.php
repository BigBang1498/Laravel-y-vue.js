<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'uploaded_by',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'file_hash',
        'description',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function getIconClassAttribute(): string
    {
        $mime = strtolower($this->mime_type);

        if (str_contains($mime, 'pdf')) {
            return 'bi-file-earmark-pdf-fill text-danger';
        }

        if (str_contains($mime, 'word') || str_contains($mime, 'document')) {
            return 'bi-file-earmark-word-fill text-primary';
        }

        if (str_contains($mime, 'excel') || str_contains($mime, 'sheet') || str_contains($mime, 'csv')) {
            return 'bi-file-earmark-excel-fill text-success';
        }

        if (str_contains($mime, 'image')) {
            return 'bi-file-earmark-image-fill text-warning';
        }

        if (str_contains($mime, 'zip') || str_contains($mime, 'rar') || str_contains($mime, 'compressed')) {
            return 'bi-file-earmark-zip-fill text-secondary';
        }

        return 'bi-file-earmark-fill text-secondary';
    }
}
