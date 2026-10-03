<?php

namespace App\Http\Requests\Response;

use Illuminate\Foundation\Http\FormRequest;

class StoreResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'response_text' => ['required', 'string', 'min:5'],
            'response_date' => ['required', 'date'],
            'sent_document_id' => ['nullable', 'exists:documents,id'],
            'mark_as_attended' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'response_text.required' => 'El texto o resolución de la respuesta es obligatorio.',
            'response_text.min' => 'El texto de la respuesta debe tener al menos 5 caracteres.',
            'response_date.required' => 'La fecha de la respuesta es obligatoria.',
            'response_date.date' => 'La fecha de la respuesta debe ser válida.',
            'sent_document_id.exists' => 'El oficio de salida seleccionado no existe.',
        ];
    }
}
