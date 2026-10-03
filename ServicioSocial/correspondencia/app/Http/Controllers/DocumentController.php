<?php

namespace App\Http\Controllers;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Http\Requests\Document\ChangeDocumentStatusRequest;
use App\Models\Department;
use App\Models\Document;
use App\Models\Expedient;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load([
            'documentType',
            'department',
            'creator',
            'assignedUser',
            'currentAssignment.assigner',
            'currentAssignment.assignedUser',
            'currentAssignment.department',
            'assignments.assigner',
            'assignments.assignedUser',
            'assignments.department',
            'history.user',
            'attachments.uploader',
            'responses.user',
            'responses.sentDocument',
            'expedients',
            'relatedDocuments.documentType',
        ]);

        $user = Auth::user();
        if ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria'])) {
            $departments = Department::active()->orderBy('name')->get();
            $assignableUsers = User::active()->with('department')->orderBy('name')->get();
        } else {
            $departments = Department::where('id', $user->department_id)->get();
            $assignableUsers = User::active()->where('department_id', $user->department_id)->orderBy('name')->get();
        }

        $availableSentDocuments = Document::sent()
            ->forUser($user)
            ->where('id', '!=', $document->id)
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        $linkedExpedientIds = $document->expedients->pluck('id')->toArray();
        $availableExpedients = Expedient::forUser($user)
            ->open()
            ->whereNotIn('id', $linkedExpedientIds)
            ->orderBy('title')
            ->get();

        return view('documents.show', compact('document', 'departments', 'assignableUsers', 'availableSentDocuments', 'availableExpedients'));
    }

    public function changeStatus(ChangeDocumentStatusRequest $request, Document $document, AuditService $auditService)
    {
        $oldStatus = $document->status;
        $newStatus = DocumentStatus::from($request->input('status'));
        $document->status = $newStatus;

        if ($newStatus === DocumentStatus::ATTENDED && !$document->attended_at) {
            $document->attended_at = now();
        }

        if ($newStatus === DocumentStatus::CLOSED && !$document->closed_at) {
            $document->closed_at = now();
        }

        $observations = $request->input('observations');
        if ($observations) {
            $prev = $document->observations ? $document->observations . "\n" : '';
            $userName = Auth::user()->name;
            $now = now()->format('d/m/Y H:i');
            $document->observations = "{$prev}[{$now} - {$userName}]: {$observations}";
        }

        $document->save();

        $description = "Cambio de estado del documento a '{$newStatus->label()}'.";
        if ($observations) {
            $description .= " Motivo: {$observations}";
        }

        $auditService->recordHistory(
            document: $document,
            user: Auth::user(),
            action: 'CAMBIO_ESTADO',
            description: $description,
            oldStatus: $oldStatus,
            newStatus: $newStatus,
            ipAddress: $request->ip()
        );

        return redirect()->route('documents.show', $document)
            ->with('success', "El estado del documento fue actualizado a '{$newStatus->label()}'.");
    }
}
