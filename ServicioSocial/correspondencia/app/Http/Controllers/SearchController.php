<?php

namespace App\Http\Controllers;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Enums\Priority;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function advanced(Request $request)
    {
        $user = Auth::user();
        $query = Document::forUser($user)->with([
            'documentType',
            'department',
            'creator',
            'assignedUser',
            'attachments',
            'expedients',
        ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhere('external_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('sender_institution', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_institution', 'like', "%{$search}%")
                  ->orWhere('observations', 'like', "%{$search}%");
            });
        }

        if ($direction = $request->input('direction')) {
            $query->where('direction', $direction);
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

        if ($assignedUserId = $request->input('assigned_user_id')) {
            $query->where('current_assigned_user_id', $assignedUserId);
        }

        if ($trafficLight = $request->input('traffic_light')) {
            if ($trafficLight === 'red') {
                $query->where(function ($q) {
                    $q->where('status', DocumentStatus::EXPIRED->value)
                      ->orWhere(function ($sub) {
                          $sub->pending()->whereNotNull('due_date')->whereDate('due_date', '<', today());
                      });
                });
            } elseif ($trafficLight === 'yellow') {
                $query->pending()
                      ->whereNotNull('due_date')
                      ->whereDate('due_date', '>=', today())
                      ->whereDate('due_date', '<=', today()->addDays(3));
            } elseif ($trafficLight === 'green') {
                $query->pending()
                      ->where(function ($q) {
                          $q->whereNull('due_date')
                            ->orWhereDate('due_date', '>', today()->addDays(3));
                      });
            }
        }

        if ($request->filled('has_attachments')) {
            if ($request->boolean('has_attachments')) {
                $query->has('attachments');
            } else {
                $query->doesntHave('attachments');
            }
        }

        if ($request->filled('has_expedient')) {
            if ($request->boolean('has_expedient')) {
                $query->has('expedients');
            } else {
                $query->doesntHave('expedients');
            }
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('document_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('document_date', '<=', $dateTo);
        }

        if ($recFrom = $request->input('received_from')) {
            $query->whereDate('received_or_sent_at', '>=', $recFrom);
        }

        if ($recTo = $request->input('received_to')) {
            $query->whereDate('received_or_sent_at', '<=', $recTo);
        }

        $results = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();

        $departments = Department::active()->orderBy('name')->get();
        $documentTypes = DocumentType::active()->orderBy('name')->get();
        $users = User::active()->orderBy('name')->get();

        return view('search.advanced', compact('results', 'departments', 'documentTypes', 'users'));
    }
}
