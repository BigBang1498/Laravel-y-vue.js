<?php

namespace App\Http\Controllers;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Enums\Priority;
use App\Http\Requests\Document\StoreSentDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Recipient;
use App\Services\AuditService;
use App\Services\FolioGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SentDocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Document::sent()->forUser($user)->with(['documentType', 'department', 'creator']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhere('external_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_institution', 'like', "%{$search}%");
            });
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        if ($typeId = $request->input('document_type_id')) {
            $query->where('document_type_id', $typeId);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $documents = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $metrics = [
            'total' => Document::sent()->forUser($user)->count(),
            'attended' => Document::sent()->forUser($user)->where('status', DocumentStatus::ATTENDED->value)->count(),
            'urgent' => Document::sent()->forUser($user)->where('priority', Priority::URGENT->value)->count(),
            'closed' => Document::sent()->forUser($user)->where('status', DocumentStatus::CLOSED->value)->count(),
        ];

        $departments = Department::active()->orderBy('name')->get();
        $documentTypes = DocumentType::active()->orderBy('name')->get();

        return view('documents.sent.index', compact('documents', 'metrics', 'departments', 'documentTypes'));
    }

    public function create()
    {
        $this->authorize('createSent', Document::class);

        $documentTypes = DocumentType::active()->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();
        $recipients = Recipient::active()->orderBy('name')->get();

        return view('documents.sent.create', compact('documentTypes', 'departments', 'recipients'));
    }

    public function store(StoreSentDocumentRequest $request, FolioGeneratorService $folioService, AuditService $auditService)
    {
        $data = $request->validated();

        $document = DB::transaction(function () use ($data, $folioService, $auditService, $request) {
            $folio = $folioService->generateNextFolio(DocumentDirection::SENT);

            $data['direction'] = DocumentDirection::SENT->value;
            $data['folio'] = $folio;
            $data['created_by'] = Auth::id();
            $data['status'] = DocumentStatus::RECEIVED->value;

            $doc = Document::create($data);

            $auditService->recordHistory(
                document: $doc,
                user: Auth::user(),
                action: 'RADICACION_SALIDA',
                description: "Correspondencia enviada registrada con folio oficial {$doc->folio}.",
                oldStatus: null,
                newStatus: DocumentStatus::RECEIVED,
                ipAddress: $request->ip()
            );

            return $doc;
        });

        return redirect()->route('documents.show', $document)
            ->with('success', "Correspondencia enviada registrada con el Folio de Salida: {$document->folio}");
    }

    public function edit(Document $document)
    {
        $this->authorize('update', $document);

        $documentTypes = DocumentType::active()->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();

        return view('documents.sent.edit', compact('document', 'documentTypes', 'departments'));
    }

    public function update(UpdateDocumentRequest $request, Document $document, AuditService $auditService)
    {
        $data = $request->validated();
        $document->update($data);

        $auditService->recordHistory(
            document: $document,
            user: Auth::user(),
            action: 'ACTUALIZACION_METADATOS',
            description: 'Metadatos del documento de salida actualizados.',
            oldStatus: $document->status,
            newStatus: $document->status,
            ipAddress: $request->ip()
        );

        return redirect()->route('documents.show', $document)
            ->with('success', "Documento {$document->folio} actualizado correctamente.");
    }
}
