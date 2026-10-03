@extends('layouts.app')

@section('title', 'Editar Documento Recibido')

@section('content')
<div class="mb-4">
    <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al Expediente
    </a>
    <h3 class="fw-bold mb-1">Editar Documento: {{ $document->folio }}</h3>
    <p class="text-secondary mb-0">Modificación autorizada de metadatos de la correspondencia recibida</p>
</div>

<form action="{{ route('received-documents.update', $document) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square text-primary"></i>
                    Información del Documento
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="external_number" class="form-label fw-semibold small">Número de Oficio / Referencia de Origen</label>
                        <input type="text" class="form-control font-monospace" id="external_number" name="external_number" value="{{ old('external_number', $document->external_number) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="document_type_id" class="form-label fw-semibold small">Tipo de Documento <span class="text-danger">*</span></label>
                        <select name="document_type_id" id="document_type_id" class="form-select" required>
                            @foreach($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ old('document_type_id', $document->document_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label fw-semibold small">Asunto <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject', $document->subject) }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold small">Extracto o Descripción</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="sender_name" class="form-label fw-semibold small">Nombre del Remitente <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_name" name="sender_name" value="{{ old('sender_name', $document->sender_name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="sender_institution" class="form-label fw-semibold small">Institución de Origen</label>
                        <input type="text" class="form-control" id="sender_institution" name="sender_institution" value="{{ old('sender_institution', $document->sender_institution) }}">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="recipient_name" class="form-label fw-semibold small">Destinatario Institucional <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $document->recipient_name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="recipient_institution" class="form-label fw-semibold small">Institución Destino</label>
                        <input type="text" class="form-control" id="recipient_institution" name="recipient_institution" value="{{ old('recipient_institution', $document->recipient_institution) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3">Términos y Clasificación</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold small d-block mb-2">Prioridad</label>
                    <select name="priority" class="form-select">
                        @foreach(App\Enums\Priority::cases() as $priority)
                            <option value="{{ $priority->value }}" {{ old('priority', $document->priority->value) === $priority->value ? 'selected' : '' }}>
                                {{ $priority->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="due_date" class="form-label fw-semibold small">Fecha Límite</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $document->due_date?->format('Y-m-d')) }}">
                </div>

                <div class="mb-3">
                    <label for="security_level" class="form-label fw-semibold small">Confidencialidad</label>
                    <select name="security_level" id="security_level" class="form-select">
                        <option value="public" {{ old('security_level', $document->security_level) === 'public' ? 'selected' : '' }}>Público</option>
                        <option value="restricted" {{ old('security_level', $document->security_level) === 'restricted' ? 'selected' : '' }}>Restringido</option>
                        <option value="confidential" {{ old('security_level', $document->security_level) === 'confidential' ? 'selected' : '' }}>Confidencial</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="observations" class="form-label fw-semibold small">Observaciones</label>
                    <textarea class="form-control" id="observations" name="observations" rows="3">{{ old('observations', $document->observations) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('documents.show', $document) }}" class="btn btn-light border w-50">Cancelar</a>
                    <button type="submit" class="btn btn-primary w-50">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
