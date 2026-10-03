<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentStorageService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function storeAttachment(
        UploadedFile $file,
        Document $document,
        User $user,
        ?string $description = null,
        ?string $ipAddress = null
    ): Attachment {
        $hash = hash_file('sha256', $file->getRealPath());
        $year = date('Y');
        $month = date('m');
        $directory = "attachments/{$year}/{$month}";

        $storedPath = $file->store($directory, 'local');

        $attachment = Attachment::create([
            'document_id' => $document->id,
            'uploaded_by' => $user->id,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'mime_type' => $file->getClientMimeType() ?: $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_hash' => $hash,
            'description' => $description,
        ]);

        $this->auditService->recordHistory(
            document: $document,
            user: $user,
            action: 'ADJUNTO_SUBIDO',
            description: "Archivo adjuntado: [{$attachment->original_name}] ({$attachment->formatted_size}) - Hash SHA-256 verificado.",
            oldStatus: $document->status,
            newStatus: $document->status,
            ipAddress: $ipAddress
        );

        return $attachment;
    }

    public function downloadAttachment(Attachment $attachment): StreamedResponse
    {
        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'El archivo físico solicitado no se encuentra en el repositorio.');
        }

        return Storage::disk('local')->download($attachment->file_path, $attachment->original_name);
    }

    public function deleteAttachment(Attachment $attachment, User $user, ?string $ipAddress = null): bool
    {
        $document = $attachment->document;
        $fileName = $attachment->original_name;

        if (Storage::disk('local')->exists($attachment->file_path)) {
            Storage::disk('local')->delete($attachment->file_path);
        }

        $deleted = $attachment->delete();

        $this->auditService->recordHistory(
            document: $document,
            user: $user,
            action: 'ADJUNTO_ELIMINADO',
            description: "Archivo adjunto eliminado: [{$fileName}].",
            oldStatus: $document->status,
            newStatus: $document->status,
            ipAddress: $ipAddress
        );

        return $deleted;
    }
}
