<?php

namespace App\Http\Controllers;

use App\Enums\DocumentDirection;
use App\Enums\DocumentPriority;
use App\Enums\DocumentStatus;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use App\Services\AuditService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private function buildReportQuery(Request $request)
    {
        $user = Auth::user();
        $query = Document::with(['department', 'documentType', 'creator', 'assignedUser', 'expedients'])
            ->forUser($user);

        if ($direction = $request->input('direction')) {
            $query->where('direction', $direction);
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        if ($typeId = $request->input('document_type_id')) {
            $query->where('document_type_id', $typeId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($assignedTo = $request->input('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('received_or_sent_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('received_or_sent_at', '<=', $dateTo);
        }

        if ($trafficLight = $request->input('traffic_light')) {
            $today = today();
            if ($trafficLight === 'red') {
                $query->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today)
                    ->whereNotIn('status', [DocumentStatus::ATTENDED->value, DocumentStatus::CLOSED->value, DocumentStatus::ARCHIVED->value]);
            } elseif ($trafficLight === 'yellow') {
                $query->whereNotNull('due_date')
                    ->whereBetween('due_date', [$today, $today->copy()->addDays(3)])
                    ->whereNotIn('status', [DocumentStatus::ATTENDED->value, DocumentStatus::CLOSED->value, DocumentStatus::ARCHIVED->value]);
            } elseif ($trafficLight === 'green') {
                $query->whereNotNull('due_date')
                    ->whereDate('due_date', '>', $today->copy()->addDays(3))
                    ->whereNotIn('status', [DocumentStatus::ATTENDED->value, DocumentStatus::CLOSED->value, DocumentStatus::ARCHIVED->value]);
            }
        }

        return $query->latest('received_or_sent_at');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->hasAnyRole(['secretaria', 'jefe_departamento', 'recepcion'])) {
            abort(403, 'No tiene autorización para generar reportes institucionales.');
        }

        $departments = Department::active()->orderBy('name')->get();
        $documentTypes = DocumentType::active()->orderBy('name')->get();
        $users = User::active()->orderBy('name')->get();

        $query = $this->buildReportQuery($request);

        $totalCount = (clone $query)->count();
        $receivedCount = (clone $query)->where('direction', DocumentDirection::RECEIVED->value)->count();
        $sentCount = (clone $query)->where('direction', DocumentDirection::SENT->value)->count();
        $pendingCount = (clone $query)->whereNotIn('status', [
            DocumentStatus::ATTENDED->value,
            DocumentStatus::CLOSED->value,
            DocumentStatus::ARCHIVED->value,
            DocumentStatus::CANCELLED->value,
        ])->count();
        $expiredCount = (clone $query)->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', [DocumentStatus::ATTENDED->value, DocumentStatus::CLOSED->value, DocumentStatus::ARCHIVED->value])
            ->count();

        $documents = $query->paginate(20)->withQueryString();

        return view('reports.index', compact(
            'documents',
            'departments',
            'documentTypes',
            'users',
            'totalCount',
            'receivedCount',
            'sentCount',
            'pendingCount',
            'expiredCount'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->hasAnyRole(['secretaria', 'jefe_departamento', 'recepcion'])) {
            abort(403, 'No tiene autorización para exportar datos.');
        }

        $query = $this->buildReportQuery($request);
        $documents = $query->get();

        AuditService::log(
            'exported_report',
            "Exportación de correspondencia en formato CSV con [{$documents->count()}] registro(s)."
        );

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-16LE',
            'Content-Disposition' => 'attachment; filename="reporte_correspondencia_' . now()->format('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($documents) {
            $handle = fopen('php://output', 'w');
            
            // UTF-16LE BOM (\xFF\xFE) for native Microsoft Excel compatibility
            fwrite($handle, chr(0xFF) . chr(0xFE));

            $columns = [
                'Folio',
                'Sentido',
                'No. Externo',
                'Tipo de Documento',
                'Dependencia',
                'Remitente',
                'Institución Remitente',
                'Destinatario',
                'Institución Destinataria',
                'Asunto',
                'Fecha del Documento',
                'Fecha Radicación / Salida',
                'Fecha Límite',
                'Prioridad',
                'Estado',
                'Responsable Asignado',
                'Expedientes',
            ];

            $headerLine = implode("\t", $columns) . "\r\n";
            fwrite($handle, mb_convert_encoding($headerLine, 'UTF-16LE', 'UTF-8'));

            foreach ($documents as $doc) {
                $row = [
                    $doc->folio,
                    $doc->direction->label(),
                    $doc->external_number ?? 'N/A',
                    $doc->documentType?->name ?? 'N/A',
                    $doc->department?->name ?? 'N/A',
                    $doc->sender_name,
                    $doc->sender_institution ?? 'N/A',
                    $doc->recipient_name,
                    $doc->recipient_institution ?? 'N/A',
                    str_replace(["\r", "\n", "\t"], ' ', $doc->subject),
                    $doc->document_date?->format('d/m/Y') ?? 'N/A',
                    $doc->received_or_sent_at?->format('d/m/Y H:i') ?? 'N/A',
                    $doc->due_date?->format('d/m/Y') ?? 'Sin término',
                    $doc->priority->label(),
                    $doc->status->label(),
                    $doc->assignedUser?->name ?? 'Sin asignar',
                    $doc->expedients->pluck('code')->implode(', ') ?: 'Ninguno',
                ];

                $line = implode("\t", $row) . "\r\n";
                fwrite($handle, mb_convert_encoding($line, 'UTF-16LE', 'UTF-8'));
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->hasAnyRole(['secretaria', 'jefe_departamento', 'recepcion'])) {
            abort(403, 'No tiene autorización para generar reportes en PDF.');
        }

        $query = $this->buildReportQuery($request);
        $documents = $query->take(100)->get();

        AuditService::log(
            'exported_report',
            "Generación de reporte institucional en PDF con [{$documents->count()}] registro(s)."
        );

        $pdf = Pdf::loadView('reports.pdf-summary', [
            'documents' => $documents,
            'generatedBy' => $user,
            'generatedAt' => now(),
            'filters' => $request->all(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte_institucional_' . now()->format('Ymd_His') . '.pdf');
    }

    public function printSummary(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->hasAnyRole(['secretaria', 'jefe_departamento', 'recepcion'])) {
            abort(403, 'No tiene autorización para generar reportes imprimibles.');
        }

        $query = $this->buildReportQuery($request);
        $documents = $query->take(200)->get();

        return view('reports.print-summary', [
            'documents' => $documents,
            'generatedBy' => $user,
            'generatedAt' => now(),
            'filters' => $request->all(),
        ]);
    }

    public function documentSlip(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['department', 'documentType', 'creator', 'assignedUser', 'expedients']);

        AuditService::log(
            'exported_report',
            "Generación de boleta oficial de radicación en PDF para el folio [{$document->folio}].",
            $document
        );

        $pdf = Pdf::loadView('reports.document-slip-pdf', [
            'document' => $document,
            'generatedBy' => Auth::user(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('boleta_radicacion_' . str_replace('-', '_', $document->folio) . '.pdf');
    }

    public function printSlip(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['department', 'documentType', 'creator', 'assignedUser', 'expedients', 'attachments']);

        return view('reports.print-slip', [
            'document' => $document,
            'generatedBy' => Auth::user(),
            'generatedAt' => now(),
        ]);
    }
}
