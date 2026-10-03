<?php

namespace App\Http\Controllers;

use App\Enums\DocumentStatus;
use App\Http\Requests\Response\StoreResponseRequest;
use App\Models\Document;
use App\Models\Response;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResponseController extends Controller
{
    public function store(StoreResponseRequest $request, Document $document, AuditService $auditService)
    {
        $this->authorize('view', $document);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $document, $auditService, $request) {
            $response = Response::create([
                'document_id' => $document->id,
                'sent_document_id' => !empty($validated['sent_document_id']) ? $validated['sent_document_id'] : null,
                'user_id' => Auth::id(),
                'response_text' => $validated['response_text'],
                'response_date' => $validated['response_date'],
            ]);

            if (!empty($validated['sent_document_id'])) {
                $document->relatedDocuments()->syncWithoutDetaching([
                    $validated['sent_document_id'] => ['relation_type' => 'response_to']
                ]);
            }

            $oldStatus = $document->status;
            if ($request->boolean('mark_as_attended')) {
                $document->status = DocumentStatus::ATTENDED;
                if (!$document->attended_at) {
                    $document->attended_at = now();
                }
                $document->save();
            }

            $description = "Respuesta oficial registrada por " . Auth::user()->name . ".";
            if (!empty($validated['sent_document_id'])) {
                $sentFolio = Document::find($validated['sent_document_id'])?->folio;
                $description .= " Vinculada al oficio de salida [{$sentFolio}].";
            }

            $auditService->recordHistory(
                document: $document,
                user: Auth::user(),
                action: 'RESPUESTA_REGISTRADA',
                description: $description,
                oldStatus: $oldStatus,
                newStatus: $document->status,
                ipAddress: $request->ip()
            );
        });

        return redirect()->route('documents.show', $document)
            ->with('success', 'La respuesta oficial ha sido registrada exitosamente.');
    }
}
