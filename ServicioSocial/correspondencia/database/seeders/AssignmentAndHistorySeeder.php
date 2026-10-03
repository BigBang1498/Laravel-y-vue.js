<?php

namespace Database\Seeders;

use App\Enums\DocumentStatus;
use App\Models\Assignment;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AssignmentAndHistorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $recepcion = User::where('email', 'recepcion@universidad.edu')->first();
        $jefeSistemas = User::where('email', 'jefe_sistemas@universidad.edu')->first();
        $empleadoSistemas = User::where('email', 'empleado_sistemas@universidad.edu')->first();
        $empleadoFinanzas = User::where('email', 'empleado_finanzas@universidad.edu')->first();

        $rectoria = Department::where('code', 'REC')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $finanzas = Department::where('code', 'FIN')->first();
        $biblioteca = Department::where('code', 'BIB')->first();

        $doc1 = Document::where('folio', 'UNI-2026-000001')->first();
        if ($doc1) {
            DocumentHistory::firstOrCreate(
                ['document_id' => $doc1->id, 'action' => 'RADICACION_ENTRADA'],
                [
                    'user_id' => $recepcion->id,
                    'old_status' => null,
                    'new_status' => DocumentStatus::RECEIVED->value,
                    'description' => 'Correspondencia recibida radicada en ventanilla oficial con folio UNI-2026-000001.',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $doc1->received_or_sent_at,
                ]
            );

            Assignment::firstOrCreate(
                ['document_id' => $doc1->id, 'assigned_to_user_id' => $admin->id],
                [
                    'assigned_by' => $recepcion->id,
                    'assigned_to_department_id' => $rectoria->id,
                    'assigned_at' => $doc1->received_or_sent_at->copy()->addMinutes(15),
                    'due_date' => $doc1->due_date,
                    'instructions' => 'Para conocimiento del Rector y confirmación de asistencia en agenda institucional.',
                    'is_current' => true,
                ]
            );

            DocumentHistory::firstOrCreate(
                ['document_id' => $doc1->id, 'action' => 'ASIGNACION'],
                [
                    'user_id' => $recepcion->id,
                    'old_status' => DocumentStatus::RECEIVED->value,
                    'new_status' => DocumentStatus::RECEIVED->value,
                    'description' => 'Documento turnado a Rectoría para el Lic. Roberto Garza con instrucciones de agenda.',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $doc1->received_or_sent_at->copy()->addMinutes(15),
                ]
            );
        }

        $doc2 = Document::where('folio', 'UNI-2026-000002')->first();
        if ($doc2) {
            DocumentHistory::firstOrCreate(
                ['document_id' => $doc2->id, 'action' => 'RADICACION_ENTRADA'],
                [
                    'user_id' => $recepcion->id,
                    'old_status' => null,
                    'new_status' => DocumentStatus::RECEIVED->value,
                    'description' => 'Radicación de solicitud urgente de auditoría de enlaces CONAHCYT.',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $doc2->received_or_sent_at,
                ]
            );

            Assignment::firstOrCreate(
                ['document_id' => $doc2->id, 'assigned_to_user_id' => $jefeSistemas->id],
                [
                    'assigned_by' => $recepcion->id,
                    'assigned_to_department_id' => $sistemas->id,
                    'assigned_at' => $doc2->received_or_sent_at->copy()->addMinutes(30),
                    'due_date' => $doc2->due_date,
                    'instructions' => 'Turnado a la Dirección de Tecnologías para dictamen urgente.',
                    'is_current' => false,
                ]
            );

            Assignment::firstOrCreate(
                ['document_id' => $doc2->id, 'assigned_to_user_id' => $empleadoSistemas->id],
                [
                    'assigned_by' => $jefeSistemas->id,
                    'assigned_to_department_id' => $sistemas->id,
                    'assigned_at' => $doc2->received_or_sent_at->copy()->addHours(2),
                    'due_date' => $doc2->due_date,
                    'instructions' => 'Ing. Morales: revisar logs de firewall y conectividad hacia el nodo CONAHCYT.',
                    'is_current' => true,
                ]
            );

            DocumentHistory::firstOrCreate(
                ['document_id' => $doc2->id, 'action' => 'REASIGNACION'],
                [
                    'user_id' => $jefeSistemas->id,
                    'old_status' => DocumentStatus::RECEIVED->value,
                    'new_status' => DocumentStatus::IN_PROCESS->value,
                    'description' => 'Documento delegado internamente a Ing. Juan Carlos Morales para dictamen técnico.',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $doc2->received_or_sent_at->copy()->addHours(2),
                ]
            );
        }

        $doc3 = Document::where('folio', 'UNI-2026-000003')->first();
        if ($doc3) {
            DocumentHistory::firstOrCreate(
                ['document_id' => $doc3->id, 'action' => 'RADICACION_ENTRADA'],
                [
                    'user_id' => $recepcion->id,
                    'old_status' => null,
                    'new_status' => DocumentStatus::RECEIVED->value,
                    'description' => 'Radicación de pliego de observaciones de fiscalización.',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $doc3->received_or_sent_at,
                ]
            );

            Assignment::firstOrCreate(
                ['document_id' => $doc3->id, 'assigned_to_user_id' => $empleadoFinanzas->id],
                [
                    'assigned_by' => $recepcion->id,
                    'assigned_to_department_id' => $finanzas->id,
                    'assigned_at' => $doc3->received_or_sent_at->copy()->addMinutes(20),
                    'due_date' => $doc3->due_date,
                    'instructions' => 'Elaborar conciliación presupuestal y justificación contable requerida.',
                    'is_current' => true,
                ]
            );

            $doc3->update(['current_assigned_user_id' => $empleadoFinanzas->id]);

            DocumentHistory::firstOrCreate(
                ['document_id' => $doc3->id, 'action' => 'VENCIMIENTO_AUTOMATICO'],
                [
                    'user_id' => null,
                    'old_status' => DocumentStatus::RECEIVED->value,
                    'new_status' => DocumentStatus::EXPIRED->value,
                    'description' => 'Plazo legal vencido el ' . $doc3->due_date->format('d/m/Y') . '. El sistema actualizó el estado a Vencido.',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $doc3->due_date->copy()->addDay()->setTime(0, 5),
                ]
            );
        }

        $doc4 = Document::where('folio', 'UNI-2026-000004')->first();
        if ($doc4) {
            DocumentHistory::firstOrCreate(
                ['document_id' => $doc4->id, 'action' => 'RADICACION_ENTRADA'],
                [
                    'user_id' => $recepcion->id,
                    'old_status' => null,
                    'new_status' => DocumentStatus::RECEIVED->value,
                    'description' => 'Radicación de resolución de acreditación SEP.',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $doc4->received_or_sent_at,
                ]
            );

            Assignment::firstOrCreate(
                ['document_id' => $doc4->id, 'assigned_to_user_id' => $admin->id],
                [
                    'assigned_by' => $recepcion->id,
                    'assigned_to_department_id' => $rectoria->id,
                    'assigned_at' => $doc4->received_or_sent_at->copy()->addMinutes(15),
                    'due_date' => $doc4->due_date,
                    'instructions' => 'Difundir constancia oficial a los comités de calidad.',
                    'is_current' => true,
                ]
            );

            DocumentHistory::firstOrCreate(
                ['document_id' => $doc4->id, 'action' => 'CAMBIO_ESTADO'],
                [
                    'user_id' => $admin->id,
                    'old_status' => DocumentStatus::RECEIVED->value,
                    'new_status' => DocumentStatus::ATTENDED->value,
                    'description' => 'Trámite solventado y turnado a resguardo institucional.',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $doc4->attended_at ?? now(),
                ]
            );
        }

        $doc5 = Document::where('folio', 'UNI-2026-000005')->first();
        if ($doc5) {
            DocumentHistory::firstOrCreate(
                ['document_id' => $doc5->id, 'action' => 'RADICACION_ENTRADA'],
                [
                    'user_id' => $recepcion->id,
                    'old_status' => null,
                    'new_status' => DocumentStatus::RECEIVED->value,
                    'description' => 'Radicación de convenio de donación bibliográfica.',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $doc5->received_or_sent_at,
                ]
            );

            Assignment::firstOrCreate(
                ['document_id' => $doc5->id, 'assigned_to_department_id' => $biblioteca->id],
                [
                    'assigned_by' => $recepcion->id,
                    'assigned_to_user_id' => null,
                    'assigned_at' => $doc5->received_or_sent_at->copy()->addMinutes(30),
                    'due_date' => $doc5->due_date,
                    'instructions' => 'Clasificar y registrar libros en catálogo bibliotecario.',
                    'is_current' => true,
                ]
            );

            DocumentHistory::firstOrCreate(
                ['document_id' => $doc5->id, 'action' => 'CAMBIO_ESTADO'],
                [
                    'user_id' => $admin->id,
                    'old_status' => DocumentStatus::ATTENDED->value,
                    'new_status' => DocumentStatus::CLOSED->value,
                    'description' => 'Expediente concluido satisfactoriamente con acuse de recepción de libros.',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $doc5->closed_at ?? now(),
                ]
            );
        }

        foreach (['UNI-SAL-2026-000001', 'UNI-SAL-2026-000002'] as $sentFolio) {
            $sentDoc = Document::where('folio', $sentFolio)->first();
            if ($sentDoc) {
                DocumentHistory::firstOrCreate(
                    ['document_id' => $sentDoc->id, 'action' => 'RADICACION_SALIDA'],
                    [
                        'user_id' => $sentDoc->created_by,
                        'old_status' => null,
                        'new_status' => DocumentStatus::RECEIVED->value,
                        'description' => "Correspondencia enviada despachada con folio {$sentDoc->folio}.",
                        'ip_address' => '127.0.0.1',
                        'created_at' => $sentDoc->received_or_sent_at,
                    ]
                );
            }
        }
    }
}
