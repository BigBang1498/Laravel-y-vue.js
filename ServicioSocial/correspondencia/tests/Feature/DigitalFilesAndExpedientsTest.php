<?php

namespace Tests\Feature;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Models\Attachment;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Expedient;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DigitalFilesAndExpedientsTest extends TestCase
{
    private function createTestDocument(Department $dept, User $creator, string $folioSuffix): Document
    {
        return Document::create([
            'direction' => DocumentDirection::RECEIVED->value,
            'folio' => 'TEST-P5-' . $folioSuffix . '-' . uniqid(),
            'external_number' => 'EXT-P5-' . uniqid(),
            'document_type_id' => DocumentType::first()->id,
            'sender_name' => 'Remitente P5',
            'recipient_name' => 'Destinatario P5',
            'subject' => 'Asunto de Prueba para Adjuntos y Expedientes',
            'document_date' => today(),
            'received_or_sent_at' => now(),
            'department_id' => $dept->id,
            'created_by' => $creator->id,
            'priority' => 'normal',
            'status' => DocumentStatus::RECEIVED->value,
        ]);
    }

    public function test_authorized_user_can_upload_attachment(): void
    {
        Storage::fake('local');
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $doc = $this->createTestDocument($sistemas, $admin, 'UP');

        $file = UploadedFile::fake()->create('oficio_oficial.pdf', 500, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('documents.attachments.store', $doc), [
            'file' => $file,
            'description' => 'Oficio original escaneado con sellos de recepción.',
        ]);

        $response->assertRedirect(route('documents.show', $doc));

        $this->assertDatabaseHas('attachments', [
            'document_id' => $doc->id,
            'original_name' => 'oficio_oficial.pdf',
            'uploaded_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('document_history', [
            'document_id' => $doc->id,
            'action' => 'ADJUNTO_SUBIDO',
        ]);

        Attachment::where('document_id', $doc->id)->delete();
        $doc->history()->delete();
        $doc->delete();
    }

    public function test_authorized_user_can_download_attachment(): void
    {
        Storage::fake('local');
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $doc = $this->createTestDocument($sistemas, $admin, 'DOWN');

        $filePath = 'attachments/test_sample.pdf';
        Storage::disk('local')->put($filePath, 'Fake PDF Binary Stream');

        $attachment = Attachment::create([
            'document_id' => $doc->id,
            'uploaded_by' => $admin->id,
            'original_name' => 'test_sample.pdf',
            'file_path' => $filePath,
            'mime_type' => 'application/pdf',
            'file_size' => 1234,
            'file_hash' => hash('sha256', 'Fake PDF Binary Stream'),
        ]);

        $response = $this->actingAs($admin)->get(route('documents.attachments.download', [$doc, $attachment]));
        $response->assertStatus(200);

        $attachment->delete();
        $doc->delete();
    }

    public function test_authorized_user_can_delete_attachment(): void
    {
        Storage::fake('local');
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $doc = $this->createTestDocument($sistemas, $admin, 'DEL');

        $filePath = 'attachments/test_delete.pdf';
        Storage::disk('local')->put($filePath, 'Delete Content');

        $attachment = Attachment::create([
            'document_id' => $doc->id,
            'uploaded_by' => $admin->id,
            'original_name' => 'test_delete.pdf',
            'file_path' => $filePath,
            'mime_type' => 'application/pdf',
            'file_size' => 500,
            'file_hash' => hash('sha256', 'Delete Content'),
        ]);

        $response = $this->actingAs($admin)->delete(route('documents.attachments.destroy', [$doc, $attachment]));
        $response->assertRedirect(route('documents.show', $doc));

        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        $this->assertDatabaseHas('document_history', [
            'document_id' => $doc->id,
            'action' => 'ADJUNTO_ELIMINADO',
        ]);

        $doc->history()->delete();
        $doc->delete();
    }

    public function test_user_can_register_official_response_and_mark_attended(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $doc = $this->createTestDocument($sistemas, $admin, 'RESP');

        $response = $this->actingAs($admin)->post(route('documents.responses.store', $doc), [
            'response_text' => 'Se emite contestación formal comunicando la resolución del comité.',
            'response_date' => date('Y-m-d\TH:i'),
            'mark_as_attended' => '1',
        ]);

        $response->assertRedirect(route('documents.show', $doc));

        $this->assertDatabaseHas('responses', [
            'document_id' => $doc->id,
            'user_id' => $admin->id,
        ]);

        $this->assertEquals(DocumentStatus::ATTENDED, $doc->fresh()->status);
        $this->assertNotNull($doc->fresh()->attended_at);

        $this->assertDatabaseHas('document_history', [
            'document_id' => $doc->id,
            'action' => 'RESPUESTA_REGISTRADA',
        ]);

        $doc->responses()->delete();
        $doc->history()->delete();
        $doc->delete();
    }

    public function test_user_can_create_expedient_with_atomic_code(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $rectoria = Department::where('code', 'REC')->first();
        $year = date('Y');

        $response = $this->actingAs($admin)->post(route('expedients.store'), [
            'title' => 'Expediente Prueba Automatizada ' . rand(100, 999),
            'description' => 'Descripción de prueba para verificar creación.',
            'department_id' => $rectoria->id,
            'responsible_user_id' => $admin->id,
            'opened_at' => date('Y-m-d'),
        ]);

        $expedient = Expedient::where('title', 'like', 'Expediente Prueba Automatizada%')->latest()->first();
        $this->assertNotNull($expedient);
        $this->assertStringStartsWith("EXP-{$year}-", $expedient->code);
        $response->assertRedirect(route('expedients.show', $expedient));
    }

    public function test_user_can_link_and_unlink_document_to_expedient(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $rectoria = Department::where('code', 'REC')->first();
        $doc = $this->createTestDocument($rectoria, $admin, 'EXP_LNK');

        $expedient = Expedient::create([
            'code' => 'EXP-TEST-' . uniqid(),
            'title' => 'Expediente Test Link',
            'department_id' => $rectoria->id,
            'responsible_user_id' => $admin->id,
            'status' => 'open',
            'opened_at' => today(),
        ]);

        $response = $this->actingAs($admin)->post(route('expedients.add-document', $expedient), [
            'document_id' => $doc->id,
        ]);

        $response->assertRedirect(route('expedients.show', $expedient));
        $this->assertDatabaseHas('expedient_documents', [
            'expedient_id' => $expedient->id,
            'document_id' => $doc->id,
        ]);
        $this->assertDatabaseHas('document_history', [
            'document_id' => $doc->id,
            'action' => 'EXPEDIENTE_VINCULADO',
        ]);

        $deleteResponse = $this->actingAs($admin)->delete(route('expedients.remove-document', [$expedient, $doc]));
        $deleteResponse->assertRedirect(route('expedients.show', $expedient));
        $this->assertDatabaseMissing('expedient_documents', [
            'expedient_id' => $expedient->id,
            'document_id' => $doc->id,
        ]);
        $this->assertDatabaseHas('document_history', [
            'document_id' => $doc->id,
            'action' => 'EXPEDIENTE_DESVINCULADO',
        ]);

        $doc->history()->delete();
        $doc->delete();
        $expedient->delete();
    }

    public function test_expedients_index_and_show_views_render(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $exp = Expedient::first();

        $indexResponse = $this->actingAs($admin)->get(route('expedients.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Expedientes Digitales');

        $showResponse = $this->actingAs($admin)->get(route('expedients.show', $exp));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($exp->code);
    }
}
