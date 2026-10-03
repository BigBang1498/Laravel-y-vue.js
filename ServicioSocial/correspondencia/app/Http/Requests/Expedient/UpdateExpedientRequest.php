<?php

namespace App\Http\Requests\Expedient;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpedientRequest extends FormRequest
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
            'status' => ['required', 'in:open,in_progress,closed,archived'],
            'opened_at' => ['required', 'date'],
            'closed_at' => ['nullable', 'date', 'after_or_equal:opened_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título del expediente es obligatorio.',
            'department_id.required' => 'El departamento responsable es obligatorio.',
            'status.required' => 'El estado del expediente es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            'opened_at.required' => 'La fecha de apertura es obligatoria.',
            'closed_at.after_or_equal' => 'La fecha de cierre no puede ser anterior a la fecha de apertura.',
        ];
    }
}
