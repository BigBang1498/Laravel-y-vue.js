<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentHistory;
use App\Models\Expedient;
use App\Models\Response;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Phase5Seeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $recepcion = User::where('email', 'recepcion@universidad.edu')->first();
        $jefeSistemas = User::where('email', 'jefe_sistemas@universidad.edu')->first();
        $empleadoFinanzas = User::where('email', 'empleado_finanzas@universidad.edu')->first();

        $rectoria = Department::where('code', 'REC')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $finanzas = Department::where('code', 'FIN')->first();

        $doc1 = Document::where('folio', 'UNI-2026-000001')->first();
        $doc2 = Document::where('folio', 'UNI-2026-000002')->first();
        $doc3 = Document::where('folio', 'UNI-2026-000003')->first();
        $doc4 = Document::where('folio', 'UNI-2026-000004')->first();
        $sent1 = Document::where('folio', 'UNI-SAL-2026-000001')->first();

        $today = Carbon::today();

        $exp1 = Expedient::updateOrCreate(
            ['code' => 'EXP-2026-000001'],
            [
                'title' => 'Convenios de Cooperación Académica e Intercambio 2026',
                'description' => 'Agrupación de gestiones, invitaciones y acuerdos bilaterales de vinculación interinstitucional.',
                'department_id' => $rectoria->id,
                'responsible_user_id' => $admin->id,
                'status' => 'open',
                'opened_at' => $today->copy()->subDays(15),
            ]
        );

        $exp2 = Expedient::updateOrCreate(
            ['code' => 'EXP-2026-000002'],
            [
                'title' => 'Auditorías de Infraestructura y Telecomunicaciones',
                'description' => 'Expediente técnico para dictámenes de conectividad de la red universitaria y enlaces nacionales.',
                'department_id' => $sistemas->id,
                'responsible_user_id' => $jefeSistemas->id,
                'status' => 'in_progress',
                'opened_at' => $today->copy()->subDays(10),
            ]
        );

        $exp3 = Expedient::updateOrCreate(
            ['code' => 'EXP-2026-000003'],
            [
                'title' => 'Fiscalización y Cierre de Ejercicio Presupuestal',
                'description' => 'Requerimientos, cédulas de auditoría y aclaraciones formuladas por órganos fiscalizadores.',
                'department_id' => $finanzas->id,
                'responsible_user_id' => $empleadoFinanzas->id,
                'status' => 'in_progress',
                'opened_at' => $today->copy()->subDays(20),
            ]
        );

        DB::table('folio_sequences')->updateOrInsert(
            ['prefix' => 'EXP', 'year' => (int) date('Y')],
            ['current_number' => 3, 'updated_at' => now(), 'created_at' => now()]
        );

        if ($doc1) {
            $exp1->documents()->syncWithoutDetaching([$doc1->id => ['added_by' => $admin->id]]);
        }

        if ($sent1) {
            $exp1->documents()->syncWithoutDetaching([$sent1->id => ['added_by' => $admin->id]]);
        }

        if ($doc2) {
            $exp2->documents()->syncWithoutDetaching([$doc2->id => ['added_by' => $jefeSistemas->id]]);
        }

        if ($doc3) {
            $exp3->documents()->syncWithoutDetaching([$doc3->id => ['added_by' => $empleadoFinanzas->id]]);
        }

        $year = date('Y');
        $month = date('m');
        $dir = "attachments/{$year}/{$month}";
        Storage::disk('local')->makeDirectory($dir);

        if ($doc1) {
            $dummyPath1 = "{$dir}/sample_invitacion_unam.pdf";
            $dummyContent1 = "%PDF-1.4 Mock PDF Content for UNI-2026-000001";
            Storage::disk('local')->put($dummyPath1, $dummyContent1);

            Attachment::updateOrCreate(
                ['document_id' => $doc1->id, 'original_name' => 'Oficio_Invitacion_UNAM_Acuse.pdf'],
                [
                    'uploaded_by' => $recepcion->id,
                    'file_path' => $dummyPath1,
                    'mime_type' => 'application/pdf',
                    'file_size' => strlen($dummyContent1),
                    'file_hash' => hash('sha256', $dummyContent1),
                    'description' => 'Acuse digitalizado con sello de recepción original de ventanilla.',
                ]
            );
        }

        if ($doc2) {
            $dummyPath2 = "{$dir}/sample_requerimiento_conahcyt.pdf";
            $dummyContent2 = "%PDF-1.4 Mock PDF Content for UNI-2026-000002";
            Storage::disk('local')->put($dummyPath2, $dummyContent2);

            Attachment::updateOrCreate(
                ['document_id' => $doc2->id, 'original_name' => 'Requerimiento_Tecnico_Puertos_Firewall.pdf'],
                [
                    'uploaded_by' => $recepcion->id,
                    'file_path' => $dummyPath2,
                    'mime_type' => 'application/pdf',
                    'file_size' => strlen($dummyContent2),
                    'file_hash' => hash('sha256', $dummyContent2),
                    'description' => 'Pliego de requerimientos y anexos de puertos de red.',
                ]
            );
        }

        if ($doc4) {
            Response::updateOrCreate(
                ['document_id' => $doc4->id],
                [
                    'sent_document_id' => null,
                    'user_id' => $admin->id,
                    'response_text' => 'Se recibe de conformidad el oficio de acreditación de calidad emitido por la SEP. Se turna constancia a la Dirección de Planeación y se notifica al H. Consejo Universitario.',
                    'response_date' => $doc4->attended_at ?? now(),
                ]
            );
        }
    }
}
