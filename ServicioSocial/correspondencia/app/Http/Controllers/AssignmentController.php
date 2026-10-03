<?php

namespace App\Http\Controllers;

use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Models\Department;
use App\Models\Document;
use App\Models\User;
use App\Services\AssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function store(StoreAssignmentRequest $request, Document $document, AssignmentService $assignmentService)
    {
        $this->authorize('assign', $document);

        $validated = $request->validated();

        $assignmentService->assign(
            document: $document,
            assignedBy: Auth::user(),
            departmentId: (int) $validated['assigned_to_department_id'],
            assignedToUserId: !empty($validated['assigned_to_user_id']) ? (int) $validated['assigned_to_user_id'] : null,
            dueDate: $validated['due_date'] ?? null,
            instructions: $validated['instructions'] ?? null,
            ipAddress: $request->ip()
        );

        return redirect()->route('documents.show', $document)
            ->with('success', 'La correspondencia ha sido turnada y asignada exitosamente.');
    }

    public function getUsersByDepartment(Department $department): JsonResponse
    {
        $users = User::active()
            ->where('department_id', $department->id)
            ->orderBy('name')
            ->get(['id', 'name', 'position', 'email']);

        return response()->json($users);
    }
}
