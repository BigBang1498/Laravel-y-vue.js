<?php

namespace App\Http\Requests\Document;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

class StoreReceivedDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('createReceived', Document::class);
    }

    public function rules(): array
    {
        return [
            'external_number' => ['nullable', 'string', 'max:100'],
            'document_type_id' => ['required', 'exists:document_types,id'],
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_institution' => ['nullable', 'string', 'max:255'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_institution' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'document_date' => ['required', 'date', 'before_or_equal:today'],
            'received_or_sent_at' => ['required', 'date'],
            'department_id' => ['required', 'exists:departments,id'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'due_date' => ['nullable', 'date', 'after_or_equal:document_date'],
            'security_level' => ['required', 'in:public,restricted,confidential'],
            'observations' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_type_id.required' => 'Seleccione el tipo de documento.',
            'sender_name.required' => 'El nombre del remitente es obligatorio.',
            'recipient_name.required' => 'El nombre del destinatario institucional es obligatorio.',
            'subject.required' => 'El asunto del documento es obligatorio.',
            'document_date.required' => 'La fecha del documento es obligatoria.',
            'document_date.before_or_equal' => 'La fecha del documento no puede ser futura.',
            'received_or_sent_at.required' => 'La fecha y hora de recepción son obligatorias.',
            'department_id.required' => 'Seleccione el departamento responsable.',
            'priority.required' => 'Indique la prioridad del documento.',
            'due_date.after_or_equal' => 'La fecha límite de atención no puede ser anterior a la fecha del documento.',
        ];
    }
}
