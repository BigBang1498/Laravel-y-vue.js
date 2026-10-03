<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('label') && !$this->filled('name')) {
            $this->merge([
                'name' => Str::slug($this->input('label'), '_'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'label.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'El identificador técnico del rol ya está en uso.',
            'name.alpha_dash' => 'El identificador sólo puede contener letras, números, guiones y guiones bajos.',
            'permissions.*.exists' => 'Uno de los permisos seleccionados no existe.',
        ];
    }
}
