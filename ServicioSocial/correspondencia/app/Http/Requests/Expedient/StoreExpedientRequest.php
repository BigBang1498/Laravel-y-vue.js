<?php

namespace App\Http\Requests\Expedient;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpedientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'department_id' => ['required', 'exists:departments,id'],
            'responsible_user_id' => ['nullable', 'exists:users,id'],
            'opened_at' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título del expediente es obligatorio.',
            'title.max' => 'El título no puede superar los 255 caracteres.',
            'department_id.required' => 'El departamento responsable es obligatorio.',
            'department_id.exists' => 'El departamento seleccionado no es válido.',
            'responsible_user_id.exists' => 'El funcionario responsable seleccionado no es válido.',
            'opened_at.required' => 'La fecha de apertura es obligatoria.',
            'opened_at.date' => 'La fecha de apertura debe ser válida.',
        ];
    }
}
