<?php

namespace App\Http\Controllers;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Enums\Priority;
use App\Http\Requests\Document\StoreReceivedDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Sender;
use App\Services\AuditService;
use App\Services\FolioGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceivedDocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Document::received()->forUser($user)->with(['documentType', 'department', 'creator', 'assignedUser']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhere('external_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('sender_institution', 'like', "%{$search}%");
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

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('document_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('document_date', '<=', $dateTo);
        }

        $documents = $query->orderByRaw("
            CASE priority
                WHEN 'urgent' THEN 1
                WHEN 'high' THEN 2
                WHEN 'normal' THEN 3
                WHEN 'low' THEN 4
                ELSE 5
            END ASC
        ")->orderBy('due_date', 'asc')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $metrics = [
            'total' => Document::received()->forUser($user)->count(),
            'pending' => Document::received()->forUser($user)->pending()->count(),
            'urgent' => Document::received()->forUser($user)->where('priority', Priority::URGENT->value)->pending()->count(),
            'expired' => Document::received()->forUser($user)->where(function ($q) {
                $q->where('status', DocumentStatus::EXPIRED->value)
                  ->orWhere(function ($sub) {
                      $sub->pending()->whereNotNull('due_date')->whereDate('due_date', '<', today());
                  });
            })->count(),
        ];

        $departments = Department::active()->orderBy('name')->get();
        $documentTypes = DocumentType::active()->orderBy('name')->get();

        return view('documents.received.index', compact('documents', 'metrics', 'departments', 'documentTypes'));
    }

    public function create()
    {
        $this->authorize('createReceived', Document::class);

        $documentTypes = DocumentType::active()->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();
        $senders = Sender::active()->orderBy('name')->get();

        return view('documents.received.create', compact('documentTypes', 'departments', 'senders'));
    }

    public function store(StoreReceivedDocumentRequest $request, FolioGeneratorService $folioService, AuditService $auditService)
    {
        $data = $request->validated();

        $document = DB::transaction(function () use ($data, $folioService, $auditService, $request) {
            $folio = $folioService->generateNextFolio(DocumentDirection::RECEIVED);

            $data['direction'] = DocumentDirection::RECEIVED->value;
            $data['folio'] = $folio;
            $data['created_by'] = Auth::id();
            $data['status'] = DocumentStatus::RECEIVED->value;

            $doc = Document::create($data);

            $auditService->recordHistory(
                document: $doc,
                user: Auth::user(),
                action: 'RADICACION_ENTRADA',
                description: "Correspondencia recibida radicada con folio oficial {$doc->folio}.",
                oldStatus: null,
                newStatus: DocumentStatus::RECEIVED,
                ipAddress: $request->ip()
            );

            return $doc;
        });

        return redirect()->route('documents.show', $document)
            ->with('success', "Correspondencia radicada exitosamente con el Folio Oficial: {$document->folio}");
    }

    public function edit(Document $document)
    {
        $this->authorize('update', $document);

        $documentTypes = DocumentType::active()->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();

        return view('documents.received.edit', compact('document', 'documentTypes', 'departments'));
    }

    public function update(UpdateDocumentRequest $request, Document $document, AuditService $auditService)
    {
        $data = $request->validated();
        $document->update($data);

        $auditService->recordHistory(
            document: $document,
            user: Auth::user(),
            action: 'ACTUALIZACION_METADATOS',
            description: 'Metadatos del documento recibidos actualizados.',
            oldStatus: $document->status,
            newStatus: $document->status,
            ipAddress: $request->ip()
        );

        return redirect()->route('documents.show', $document)
            ->with('success', "Documento {$document->folio} actualizado correctamente.");
    }
}
