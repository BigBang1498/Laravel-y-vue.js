<?php

namespace App\Http\Requests\Attachment;

use Illuminate\Foundation\Http\FormRequest;

class UploadAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:20480',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,7z,jpg,jpeg,png,txt,csv',
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Debe seleccionar un archivo para adjuntar.',
            'file.file' => 'El elemento cargado debe ser un archivo válido.',
            'file.max' => 'El tamaño del archivo no puede superar los 20 MB.',
            'file.mimes' => 'Tipo de archivo no permitido. Se aceptan documentos PDF, Office, imágenes, archivos comprimidos y texto.',
            'description.max' => 'La descripción no puede superar los 255 caracteres.',
        ];
    }
}
