<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expedient\StoreExpedientRequest;
use App\Http\Requests\Expedient\UpdateExpedientRequest;
use App\Models\Department;
use App\Models\Document;
use App\Models\Expedient;
use App\Models\User;
use App\Services\AuditService;
use App\Services\ExpedientFolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpedientController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Expedient::forUser($user)->with(['department', 'responsibleUser'])->withCount('documents');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $expedients = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $metrics = [
            'total' => Expedient::forUser($user)->count(),
            'open' => Expedient::forUser($user)->where('status', 'open')->count(),
            'in_progress' => Expedient::forUser($user)->where('status', 'in_progress')->count(),
            'closed' => Expedient::forUser($user)->where('status', 'closed')->count(),
        ];

        $departments = Department::active()->orderBy('name')->get();

        return view('expedients.index', compact('expedients', 'metrics', 'departments'));
    }

    public function create()
    {
        $this->authorize('create', Expedient::class);

        $user = Auth::user();
        if ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria'])) {
            $departments = Department::active()->orderBy('name')->get();
            $users = User::active()->orderBy('name')->get();
        } else {
            $departments = Department::where('id', $user->department_id)->get();
            $users = User::active()->where('department_id', $user->department_id)->orderBy('name')->get();
        }

        return view('expedients.create', compact('departments', 'users'));
    }

    public function store(StoreExpedientRequest $request, ExpedientFolioService $folioService)
    {
        $this->authorize('create', Expedient::class);

        $validated = $request->validated();
        $code = $folioService->generateNextCode();

        $expedient = Expedient::create([
            'code' => $code,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'department_id' => $validated['department_id'],
            'responsible_user_id' => $validated['responsible_user_id'] ?? Auth::id(),
            'status' => 'open',
            'opened_at' => $validated['opened_at'],
        ]);

        return redirect()->route('expedients.show', $expedient)
            ->with('success', "Expediente digital {$expedient->code} creado exitosamente.");
    }

    public function show(Expedient $expedient)
    {
        $this->authorize('view', $expedient);

        $expedient->load([
            'department',
            'responsibleUser',
            'documents.documentType',
            'documents.department',
            'documents.creator',
            'documents.assignedUser',
        ]);

        $user = Auth::user();
        $linkedDocIds = $expedient->documents->pluck('id')->toArray();

        $availableDocuments = Document::forUser($user)
            ->whereNotIn('id', $linkedDocIds)
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return view('expedients.show', compact('expedient', 'availableDocuments'));
    }

    public function edit(Expedient $expedient)
    {
        $this->authorize('update', $expedient);

        $user = Auth::user();
        if ($user->isAdmin() || $user->hasRole('secretaria')) {
            $departments = Department::active()->orderBy('name')->get();
            $users = User::active()->orderBy('name')->get();
        } else {
            $departments = Department::where('id', $user->department_id)->get();
            $users = User::active()->where('department_id', $user->department_id)->orderBy('name')->get();
        }

        return view('expedients.edit', compact('expedient', 'departments', 'users'));
    }

    public function update(UpdateExpedientRequest $request, Expedient $expedient)
    {
        $this->authorize('update', $expedient);

        $validated = $request->validated();
        $expedient->update($validated);

        return redirect()->route('expedients.show', $expedient)
            ->with('success', "Expediente {$expedient->code} actualizado correctamente.");
    }

    public function addDocument(Request $request, Expedient $expedient, AuditService $auditService)
    {
        $this->authorize('manageDocuments', $expedient);

        $request->validate([
            'document_id' => ['required', 'exists:documents,id'],
        ]);

        $document = Document::findOrFail($request->input('document_id'));

        $expedient->documents()->syncWithoutDetaching([
            $document->id => ['added_by' => Auth::id()]
        ]);

        $auditService->recordHistory(
            document: $document,
            user: Auth::user(),
            action: 'EXPEDIENTE_VINCULADO',
            description: "Documento incorporado al expediente digital [{$expedient->code} - {$expedient->title}].",
            oldStatus: $document->status,
            newStatus: $document->status,
            ipAddress: $request->ip()
        );

        return redirect()->route('expedients.show', $expedient)
            ->with('success', "Documento {$document->folio} vinculado al expediente exitosamente.");
    }

    public function removeDocument(Request $request, Expedient $expedient, Document $document, AuditService $auditService)
    {
        $this->authorize('manageDocuments', $expedient);

        $expedient->documents()->detach($document->id);

        $auditService->recordHistory(
            document: $document,
            user: Auth::user(),
            action: 'EXPEDIENTE_DESVINCULADO',
            description: "Documento retirado del expediente digital [{$expedient->code}].",
            oldStatus: $document->status,
            newStatus: $document->status,
            ipAddress: $request->ip()
        );

        return redirect()->route('expedients.show', $expedient)
            ->with('success', "Documento {$document->folio} retirado del expediente.");
    }
}
