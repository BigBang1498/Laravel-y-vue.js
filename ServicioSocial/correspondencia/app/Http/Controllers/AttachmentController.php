<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attachment\UploadAttachmentRequest;
use App\Models\Attachment;
use App\Models\Document;
use App\Services\AttachmentStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function store(UploadAttachmentRequest $request, Document $document, AttachmentStorageService $storageService)
    {
        $this->authorize('upload', [Attachment::class, $document]);

        $storageService->storeAttachment(
            file: $request->file('file'),
            document: $document,
            user: Auth::user(),
            description: $request->input('description'),
            ipAddress: $request->ip()
        );

        return redirect()->route('documents.show', $document)
            ->with('success', 'Archivo digital adjuntado y protegido con hash SHA-256 exitosamente.');
    }

    public function download(Document $document, Attachment $attachment, AttachmentStorageService $storageService): StreamedResponse
    {
        if ($attachment->document_id !== $document->id) {
            abort(404);
        }

        $this->authorize('download', $attachment);

        return $storageService->downloadAttachment($attachment);
    }

    public function destroy(Request $request, Document $document, Attachment $attachment, AttachmentStorageService $storageService)
    {
        if ($attachment->document_id !== $document->id) {
            abort(404);
        }

        $this->authorize('delete', $attachment);

        $storageService->deleteAttachment(
            attachment: $attachment,
            user: Auth::user(),
            ipAddress: $request->ip()
        );

        return redirect()->route('documents.show', $document)
            ->with('success', 'El archivo digital adjunto ha sido eliminado correctamente.');
    }
}
