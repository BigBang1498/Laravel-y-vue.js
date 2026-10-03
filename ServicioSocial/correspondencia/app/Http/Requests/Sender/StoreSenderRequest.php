<?php

namespace App\Http\Requests\Sender;

use Illuminate\Foundation\Http\FormRequest;

class StoreSenderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isAdmin() || $this->user()->hasAnyRole(['recepcion', 'secretaria']));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del remitente es obligatorio.',
            'institution.required' => 'La institución u organización de origen es obligatoria.',
            'email.email' => 'Ingrese una dirección de correo válida.',
        ];
    }
}
