<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $document = $this->route('document');
        return $this->user()->can('update', $document);
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
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'due_date' => ['nullable', 'date'],
            'security_level' => ['required', 'in:public,restricted,confidential'],
            'observations' => ['nullable', 'string'],
        ];
    }
}
