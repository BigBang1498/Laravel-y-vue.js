<?php

namespace App\Http\Requests\Document;

use App\Enums\DocumentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeDocumentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $document = $this->route('document');
        return $this->user()->can('changeStatus', $document);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(DocumentStatus::class)],
            'observations' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $document = $this->route('document');
            $targetStatus = DocumentStatus::tryFrom($this->input('status'));

            if ($targetStatus && !$document->canTransitionTo($targetStatus) && !$this->user()->isAdmin()) {
                $validator->errors()->add(
                    'status',
                    "No está permitida la transición de estado desde '{$document->status->label()}' hacia '{$targetStatus->label()}'."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Debe seleccionar el nuevo estado del documento.',
        ];
    }
}
