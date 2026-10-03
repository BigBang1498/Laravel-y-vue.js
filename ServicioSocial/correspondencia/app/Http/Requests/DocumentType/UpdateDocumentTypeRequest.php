<?php

namespace App\Http\Requests\DocumentType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $typeId = $this->route('document_type')->id ?? $this->route('document_type');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('document_types', 'name')->ignore($typeId)],
            'code' => ['required', 'string', 'max:20', Rule::unique('document_types', 'code')->ignore($typeId)],
            'description' => ['nullable', 'string'],
            'default_days_to_expire' => ['required', 'integer', 'min:1', 'max:365'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del tipo de documento es obligatorio.',
            'name.unique' => 'Ya existe un tipo de documento con este nombre.',
            'code.required' => 'El código de identificación es obligatorio.',
            'code.unique' => 'Ya existe un tipo de documento con este código.',
            'default_days_to_expire.required' => 'Indique los días predeterminados de vencimiento.',
            'default_days_to_expire.min' => 'El término de vencimiento debe ser al menos de 1 día.',
        ];
    }
}
