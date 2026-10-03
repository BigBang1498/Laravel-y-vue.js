<?php

namespace App\Http\Controllers;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Enums\Priority;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentHistory;
use App\Models\DocumentType;
use App\Models\Expedient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['roles', 'department']);

        $metrics = [
            'received' => Document::received()->forUser($user)->count(),
            'sent' => Document::sent()->forUser($user)->count(),
            'pending' => Document::forUser($user)->pending()->count(),
            'due_soon' => Document::forUser($user)->pending()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '>=', today())
                ->whereDate('due_date', '<=', today()->addDays(3))
                ->count(),
            'expired' => Document::forUser($user)->where(function ($q) {
                $q->where('status', DocumentStatus::EXPIRED->value)
                  ->orWhere(function ($sub) {
                      $sub->pending()->whereNotNull('due_date')->whereDate('due_date', '<', today());
                  });
            })->count(),
            'expedients' => Expedient::forUser($user)->open()->count(),
        ];

        $priorityBreakdown = [
            'urgent' => Document::forUser($user)->pending()->where('priority', Priority::URGENT->value)->count(),
            'high' => Document::forUser($user)->pending()->where('priority', Priority::HIGH->value)->count(),
            'normal' => Document::forUser($user)->pending()->where('priority', Priority::NORMAL->value)->count(),
            'low' => Document::forUser($user)->pending()->where('priority', Priority::LOW->value)->count(),
        ];

        $recentDocuments = Document::forUser($user)
            ->with(['documentType', 'department', 'assignedUser'])
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        $recentHistory = DocumentHistory::with(['document', 'user'])
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        $departmentBreakdown = Department::active()
            ->withCount(['documents as total_docs'])
            ->orderBy('total_docs', 'desc')
            ->limit(6)
            ->get();

        $departmentStaffWorkload = collect();
        if ($user->hasRole('jefe_departamento') || $user->isAdmin()) {
            $deptId = $user->isAdmin() ? ($departmentBreakdown->first()?->id ?? 1) : $user->department_id;
            $departmentStaffWorkload = User::active()
                ->where('department_id', $deptId)
                ->withCount(['assignedDocuments as pending_count' => function ($q) {
                    $q->pending();
                }])
                ->orderBy('pending_count', 'desc')
                ->get();
        }

        $myPendingDocuments = collect();
        if ($user->hasRole('empleado')) {
            $myPendingDocuments = Document::where('current_assigned_user_id', $user->id)
                ->pending()
                ->with(['documentType', 'department'])
                ->orderBy('due_date', 'asc')
                ->limit(6)
                ->get();
        }

        return view('dashboard', compact(
            'user',
            'metrics',
            'priorityBreakdown',
            'recentDocuments',
            'recentHistory',
            'departmentBreakdown',
            'departmentStaffWorkload',
            'myPendingDocuments'
        ));
    }
}
