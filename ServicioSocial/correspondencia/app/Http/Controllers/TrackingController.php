<?php

namespace App\Http\Controllers;

use App\Enums\DocumentStatus;
use App\Enums\Priority;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function myDocuments(Request $request)
    {
        $user = Auth::user();
        $query = Document::where('current_assigned_user_id', $user->id)
            ->with(['documentType', 'department', 'creator', 'currentAssignment.assigner']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhere('external_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%");
            });
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
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
            'total' => Document::where('current_assigned_user_id', $user->id)->count(),
            'pending' => Document::where('current_assigned_user_id', $user->id)->pending()->count(),
            'due_soon' => Document::where('current_assigned_user_id', $user->id)
                ->pending()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '>=', today())
                ->whereDate('due_date', '<=', today()->addDays(3))
                ->count(),
            'expired' => Document::where('current_assigned_user_id', $user->id)
                ->where(function ($q) {
                    $q->where('status', DocumentStatus::EXPIRED->value)
                      ->orWhere(function ($sub) {
                          $sub->pending()->whereNotNull('due_date')->whereDate('due_date', '<', today());
                      });
                })->count(),
        ];

        return view('tracking.my_documents', compact('documents', 'metrics'));
    }

    public function pending(Request $request)
    {
        $user = Auth::user();
        $query = Document::pending()->forUser($user)->with(['documentType', 'department', 'creator', 'assignedUser']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhere('external_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
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
            'total_pending' => Document::pending()->forUser($user)->count(),
            'urgent' => Document::pending()->forUser($user)->where('priority', Priority::URGENT->value)->count(),
            'high' => Document::pending()->forUser($user)->where('priority', Priority::HIGH->value)->count(),
            'due_soon' => Document::pending()->forUser($user)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '>=', today())
                ->whereDate('due_date', '<=', today()->addDays(3))
                ->count(),
        ];

        $departments = Department::active()->orderBy('name')->get();
        $documentTypes = DocumentType::active()->orderBy('name')->get();

        return view('tracking.pending', compact('documents', 'metrics', 'departments', 'documentTypes'));
    }

    public function expired(Request $request)
    {
        $user = Auth::user();
        $query = Document::forUser($user)
            ->where(function ($q) {
                $q->where('status', DocumentStatus::EXPIRED->value)
                  ->orWhere(function ($sub) {
                      $sub->pending()->whereNotNull('due_date')->whereDate('due_date', '<', today());
                  });
            })
            ->with(['documentType', 'department', 'creator', 'assignedUser']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhere('external_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        $documents = $query->orderBy('due_date', 'asc')->paginate(10)->withQueryString();

        $metrics = [
            'total_expired' => Document::forUser($user)
                ->where(function ($q) {
                    $q->where('status', DocumentStatus::EXPIRED->value)
                      ->orWhere(function ($sub) {
                          $sub->pending()->whereNotNull('due_date')->whereDate('due_date', '<', today());
                      });
                })->count(),
            'urgent_expired' => Document::forUser($user)
                ->where('priority', Priority::URGENT->value)
                ->where(function ($q) {
                    $q->where('status', DocumentStatus::EXPIRED->value)
                      ->orWhere(function ($sub) {
                          $sub->pending()->whereNotNull('due_date')->whereDate('due_date', '<', today());
                      });
                })->count(),
        ];

        $departments = Department::active()->orderBy('name')->get();

        return view('tracking.expired', compact('documents', 'metrics', 'departments'));
    }
}
