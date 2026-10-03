<?php

namespace App\Http\Requests\Assignment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assigned_to_department_id' => ['required', 'exists:departments,id'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'instructions' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_to_department_id.required' => 'Debe seleccionar el departamento destinatario.',
            'assigned_to_department_id.exists' => 'El departamento seleccionado no es válido.',
            'assigned_to_user_id.exists' => 'El funcionario seleccionado no es válido.',
            'due_date.date' => 'La fecha límite debe ser una fecha válida.',
            'due_date.after_or_equal' => 'La fecha límite no puede ser anterior a la fecha de hoy.',
            'instructions.max' => 'Las instrucciones no pueden superar los 2000 caracteres.',
        ];
    }
}
