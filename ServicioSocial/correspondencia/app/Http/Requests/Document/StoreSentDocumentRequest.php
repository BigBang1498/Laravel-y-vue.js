<?php

namespace App\Http\Requests\Document;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

class StoreSentDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('createSent', Document::class);
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
            'document_date' => ['required', 'date'],
            'received_or_sent_at' => ['required', 'date'],
            'department_id' => ['required', 'exists:departments,id'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'security_level' => ['required', 'in:public,restricted,confidential'],
            'observations' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_type_id.required' => 'Seleccione el tipo de documento.',
            'sender_name.required' => 'El funcionario o emisor institucional es obligatorio.',
            'recipient_name.required' => 'El nombre del destinatario externo es obligatorio.',
            'subject.required' => 'El asunto del documento es obligatorio.',
            'document_date.required' => 'La fecha del documento es obligatoria.',
            'department_id.required' => 'Seleccione el departamento emisor.',
            'priority.required' => 'Indique la prioridad.',
        ];
    }
}
