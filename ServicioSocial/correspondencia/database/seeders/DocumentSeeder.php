<?php

namespace Database\Seeders;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Enums\Priority;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $recepcion = User::where('email', 'recepcion@universidad.edu')->first();
        $jefeSistemas = User::where('email', 'jefe_sistemas@universidad.edu')->first();
        $empleadoSistemas = User::where('email', 'empleado_sistemas@universidad.edu')->first();

        $rectoria = Department::where('code', 'REC')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $finanzas = Department::where('code', 'FIN')->first();
        $biblioteca = Department::where('code', 'BIB')->first();

        $oficio = DocumentType::where('code', 'OF')->first();
        $invitacion = DocumentType::where('code', 'INV')->first();
        $solicitud = DocumentType::where('code', 'SOL')->first();
        $circular = DocumentType::where('code', 'CIR')->first();

        $today = Carbon::today();

        $documents = [
            [
                'direction' => DocumentDirection::RECEIVED->value,
                'folio' => 'UNI-2026-000001',
                'external_number' => 'UNAM/SG/2026/0120',
                'document_type_id' => $invitacion->id,
                'sender_name' => 'Dr. Alejandro Villar Morales',
                'sender_institution' => 'Universidad Nacional Autónoma de México',
                'recipient_name' => 'Lic. Roberto Garza',
                'recipient_institution' => config('app.name', 'Universidad'),
                'subject' => 'Invitación a Ceremonia Anual de Rectores y Coloquio Académico',
                'description' => 'Invitación formal al Encuentro Nacional de Rectores a celebrarse en Ciudad Universitaria.',
                'document_date' => $today->copy()->subDays(5)->format('Y-m-d'),
                'received_or_sent_at' => $today->copy()->subDays(4)->setTime(9, 30),
                'department_id' => $rectoria->id,
                'created_by' => $recepcion->id,
                'current_assigned_user_id' => $admin->id,
                'priority' => Priority::HIGH->value,
                'status' => DocumentStatus::RECEIVED->value,
                'due_date' => $today->copy()->addDays(10)->format('Y-m-d'),
                'security_level' => 'public',
                'observations' => 'Documento original radicado con acuse físico en ventanilla.',
            ],
            [
                'direction' => DocumentDirection::RECEIVED->value,
                'folio' => 'UNI-2026-000002',
                'external_number' => 'CONAHCYT/SIST/2026/089',
                'document_type_id' => $solicitud->id,
                'sender_name' => 'Dra. Sofía Valdés Noriega',
                'sender_institution' => 'CONAHCYT',
                'recipient_name' => 'Ing. Juan Carlos Morales',
                'recipient_institution' => config('app.name', 'Universidad'),
                'subject' => 'Requerimiento de Auditoría de Seguridad en Enlace de Datos',
                'description' => 'Solicitud urgente de reporte sobre el estado de firewalls y conectividad de la red universitaria.',
                'document_date' => $today->copy()->subDays(3)->format('Y-m-d'),
                'received_or_sent_at' => $today->copy()->subDays(2)->setTime(10, 15),
                'department_id' => $sistemas->id,
                'created_by' => $recepcion->id,
                'current_assigned_user_id' => $empleadoSistemas->id,
                'priority' => Priority::URGENT->value,
                'status' => DocumentStatus::IN_PROCESS->value,
                'due_date' => $today->copy()->addDays(2)->format('Y-m-d'),
                'security_level' => 'restricted',
                'observations' => 'Turnado a desarrollo e infraestructura para dictamen técnico.',
            ],
            [
                'direction' => DocumentDirection::RECEIVED->value,
                'folio' => 'UNI-2026-000003',
                'external_number' => 'OFS/FIN/2026/0034',
                'document_type_id' => $oficio->id,
                'sender_name' => 'Lic. Brenda Morales Díaz',
                'sender_institution' => 'Órgano de Fiscalización Superior',
                'recipient_name' => 'C.P. Fernando Valenzuela',
                'recipient_institution' => config('app.name', 'Universidad'),
                'subject' => 'Pliego de Observaciones sobre Ejercicio Presupuestal Trimestral',
                'description' => 'Requerimiento de aclaraciones contables sobre las partidas de adquisiciones.',
                'document_date' => $today->copy()->subDays(20)->format('Y-m-d'),
                'received_or_sent_at' => $today->copy()->subDays(18)->setTime(11, 0),
                'department_id' => $finanzas->id,
                'created_by' => $recepcion->id,
                'current_assigned_user_id' => null,
                'priority' => Priority::HIGH->value,
                'status' => DocumentStatus::EXPIRED->value,
                'due_date' => $today->copy()->subDays(3)->format('Y-m-d'),
                'security_level' => 'restricted',
                'observations' => 'Plazo legal vencido el ' . $today->copy()->subDays(3)->format('d/m/Y') . '. Requiere atención prioritaria.',
            ],
            [
                'direction' => DocumentDirection::RECEIVED->value,
                'folio' => 'UNI-2026-000004',
                'external_number' => 'SEP/DGESU/2026/0412',
                'document_type_id' => $oficio->id,
                'sender_name' => 'Dra. María Antonieta Rosas',
                'sender_institution' => 'Secretaría de Educación Pública',
                'recipient_name' => 'Rectoría Universitaria',
                'recipient_institution' => config('app.name', 'Universidad'),
                'subject' => 'Acreditación del Programa Institucional de Calidad',
                'description' => 'Dictamen favorable respecto a la evaluación del programa educativo.',
                'document_date' => $today->copy()->subDays(15)->format('Y-m-d'),
                'received_or_sent_at' => $today->copy()->subDays(14)->setTime(8, 45),
                'department_id' => $rectoria->id,
                'created_by' => $recepcion->id,
                'current_assigned_user_id' => $admin->id,
                'priority' => Priority::NORMAL->value,
                'status' => DocumentStatus::ATTENDED->value,
                'due_date' => $today->copy()->subDays(2)->format('Y-m-d'),
                'attended_at' => $today->copy()->subDays(1)->setTime(14, 0),
                'security_level' => 'public',
                'observations' => 'Atendido formalmente y remitida constancia a Secretaría Académica.',
            ],
            [
                'direction' => DocumentDirection::RECEIVED->value,
                'folio' => 'UNI-2026-000005',
                'external_number' => 'DON/2026/001',
                'document_type_id' => $oficio->id,
                'sender_name' => 'Lic. Gerardo Ortiz Castillo',
                'sender_institution' => 'Gobierno del Estado',
                'recipient_name' => 'Dirección de Biblioteca',
                'recipient_institution' => config('app.name', 'Universidad'),
                'subject' => 'Donación de Colección Histórica Bibliográfica',
                'description' => 'Acuerdo de donación de 500 volúmenes de historia regional para la Biblioteca Universitaria.',
                'document_date' => $today->copy()->subDays(30)->format('Y-m-d'),
                'received_or_sent_at' => $today->copy()->subDays(28)->setTime(12, 0),
                'department_id' => $biblioteca->id,
                'created_by' => $recepcion->id,
                'current_assigned_user_id' => null,
                'priority' => Priority::LOW->value,
                'status' => DocumentStatus::CLOSED->value,
                'due_date' => $today->copy()->subDays(10)->format('Y-m-d'),
                'attended_at' => $today->copy()->subDays(12)->setTime(16, 0),
                'closed_at' => $today->copy()->subDays(5)->setTime(10, 0),
                'security_level' => 'public',
                'observations' => 'Libros inventariados y sellados en sala de consulta.',
            ],

            [
                'direction' => DocumentDirection::SENT->value,
                'folio' => 'UNI-SAL-2026-000001',
                'external_number' => 'REC/SAL/2026/001',
                'document_type_id' => $oficio->id,
                'sender_name' => $admin->name,
                'sender_institution' => config('app.name', 'Universidad'),
                'recipient_name' => 'Dra. Carmen Enedina Rodríguez Armenta',
                'recipient_institution' => 'Subsecretaría de Educación Superior (SES)',
                'subject' => 'Remisión de Informe Trimestral de Matrícula y Avance Presupuestal',
                'description' => 'Envío formal de estadísticas auditadas del ciclo escolar vigente.',
                'document_date' => $today->copy()->subDays(7)->format('Y-m-d'),
                'received_or_sent_at' => $today->copy()->subDays(7)->setTime(11, 30),
                'department_id' => $rectoria->id,
                'created_by' => $admin->id,
                'current_assigned_user_id' => null,
                'priority' => Priority::NORMAL->value,
                'status' => DocumentStatus::ATTENDED->value,
                'due_date' => null,
                'security_level' => 'public',
                'observations' => 'Despachado vía mensajería con número de guía MX-992384.',
            ],
            [
                'direction' => DocumentDirection::SENT->value,
                'folio' => 'UNI-SAL-2026-000002',
                'external_number' => 'SIS/SAL/2026/015',
                'document_type_id' => $solicitud->id,
                'sender_name' => $jefeSistemas->name,
                'sender_institution' => config('app.name', 'Universidad'),
                'recipient_name' => 'Dr. Luis Armando González Placencia',
                'recipient_institution' => 'ANUIES',
                'subject' => 'Postulación para Conectividad e Internet de Alta Velocidad para Red Nacional',
                'description' => 'Solicitud de incorporación al nodo metropolitano de fibra óptica.',
                'document_date' => $today->copy()->subDays(1)->format('Y-m-d'),
                'received_or_sent_at' => $today->copy()->subDays(1)->setTime(15, 45),
                'department_id' => $sistemas->id,
                'created_by' => $jefeSistemas->id,
                'current_assigned_user_id' => null,
                'priority' => Priority::URGENT->value,
                'status' => DocumentStatus::RECEIVED->value,
                'due_date' => null,
                'security_level' => 'public',
                'observations' => 'Entregado en Oficialía de Partes de ANUIES.',
            ],
        ];

        foreach ($documents as $doc) {
            Document::updateOrCreate(['folio' => $doc['folio']], $doc);
        }

        DB::table('folio_sequences')->updateOrInsert(
            ['prefix' => 'UNI', 'year' => (int) date('Y')],
            ['current_number' => 5, 'updated_at' => now(), 'created_at' => now()]
        );

        DB::table('folio_sequences')->updateOrInsert(
            ['prefix' => 'UNI-SAL', 'year' => (int) date('Y')],
            ['current_number' => 2, 'updated_at' => now(), 'created_at' => now()]
        );
    }
}
