@extends('layouts.app')

@section('title', 'Radicar Correspondencia Recibida')

@section('content')
<div class="mb-4">
    <a href="{{ route('received-documents.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver a Recibidos
    </a>
    <h3 class="fw-bold mb-1">Radicar Correspondencia Recibida (Entrada)</h3>
    <p class="text-secondary mb-0">Registro oficial de ingreso documental con generación automática de folio institucional</p>
</div>

<form action="{{ route('received-documents.store') }}" method="POST">
    @csrf

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-file-text-fill text-primary"></i>
                    Información General del Documento
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="external_number" class="form-label fw-semibold small">Número de Oficio / Referencia de Origen</label>
                        <input type="text" class="form-control font-monospace @error('external_number') is-invalid @enderror" id="external_number" name="external_number" value="{{ old('external_number') }}" placeholder="Ej. SEP/DGESU/2026/0412">
                        <small class="text-muted">Número oficial que ostenta el documento físico recibido.</small>
                        @error('external_number')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="document_type_id" class="form-label fw-semibold small">Tipo de Documento <span class="text-danger">*</span></label>
                        <select name="document_type_id" id="document_type_id" class="form-select @error('document_type_id') is-invalid @enderror" required>
                            <option value="">-- Seleccione tipo --</option>
                            @foreach($documentTypes as $type)
                                <option value="{{ $type->id }}" data-days="{{ $type->default_days_to_expire }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ $type->default_days_to_expire }} días de término)
                                </option>
                            @endforeach
                        </select>
                        @error('document_type_id')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label fw-semibold small">Asunto / Síntesis del Documento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="Resumen claro y conciso del contenido del documento...">
                    @error('subject')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold small">Extracto o Descripción Detallada</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Antecedentes, petición formal, anexos mencionados...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <h5 class="fw-bold mb-3 border-top pt-3 d-flex align-items-center gap-2">
                    <i class="bi bi-person-lines-fill text-primary"></i>
                    Remitente y Destinatario
                </h5>

                <div class="mb-3 bg-light p-3 rounded-3 border">
                    <label for="sender_catalog" class="form-label fw-semibold small mb-1">
                        <i class="bi bi-search me-1"></i> Autocompletar desde Catálogo de Remitentes
                    </label>
                    <select id="sender_catalog" class="form-select form-select-sm">
                        <option value="">-- Seleccionar remitente registrado previamente (Opcional) --</option>
                        @foreach($senders as $sender)
                            <option value="{{ $sender->id }}" data-name="{{ $sender->name }}" data-institution="{{ $sender->institution }}">
                                {{ $sender->name }} — {{ $sender->institution }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="sender_name" class="form-label fw-semibold small">Nombre del Remitente <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('sender_name') is-invalid @enderror" id="sender_name" name="sender_name" value="{{ old('sender_name') }}" required placeholder="Nombre de la persona o funcionario emisor">
                        @error('sender_name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="sender_institution" class="form-label fw-semibold small">Institución / Dependencia de Origen</label>
                        <input type="text" class="form-control @error('sender_institution') is-invalid @enderror" id="sender_institution" name="sender_institution" value="{{ old('sender_institution') }}" placeholder="Ej. SEP, UNAM, Gobierno del Estado">
                        @error('sender_institution')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="recipient_name" class="form-label fw-semibold small">Destinatario Institucional <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', 'Rectoría / Universidad') }}" required>
                        @error('recipient_name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="recipient_institution" class="form-label fw-semibold small">Institución Destino</label>
                        <input type="text" class="form-control @error('recipient_institution') is-invalid @enderror" id="recipient_institution" name="recipient_institution" value="{{ old('recipient_institution', config('app.name', 'Universidad')) }}">
                        @error('recipient_institution')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-sliders text-primary"></i>
                    Clasificación y Términos
                </h5>

                <div class="mb-3">
                    <label for="department_id" class="form-label fw-semibold small">Departamento Responsable <span class="text-danger">*</span></label>
                    <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                        <option value="">-- Seleccionar departamento --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small d-block mb-2">Prioridad del Documento <span class="text-danger">*</span></label>
                    <div class="d-flex flex-column gap-2">
                        @foreach(App\Enums\Priority::cases() as $priority)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="priority" id="priority_{{ $priority->value }}" value="{{ $priority->value }}"
                                    {{ old('priority', 'normal') === $priority->value ? 'checked' : '' }}>
                                <label class="form-check-label" for="priority_{{ $priority->value }}">
                                    <span class="badge {{ $priority->badgeClass() }} px-2 py-1">
                                        <i class="bi {{ $priority->icon() }} me-1"></i> {{ $priority->label() }}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label for="security_level" class="form-label fw-semibold small">Nivel de Confidencialidad <span class="text-danger">*</span></label>
                    <select name="security_level" id="security_level" class="form-select" required>
                        <option value="public" {{ old('security_level', 'public') === 'public' ? 'selected' : '' }}>Público Institucional</option>
                        <option value="restricted" {{ old('security_level') === 'restricted' ? 'selected' : '' }}>Restringido</option>
                        <option value="confidential" {{ old('security_level') === 'confidential' ? 'selected' : '' }}>Confidencial</option>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <label for="document_date" class="form-label fw-semibold small">Fecha del Documento Físico <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('document_date') is-invalid @enderror" id="document_date" name="document_date" value="{{ old('document_date', date('Y-m-d')) }}" required>
                        @error('document_date')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="received_or_sent_at" class="form-label fw-semibold small">Fecha y Hora de Radicación <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('received_or_sent_at') is-invalid @enderror" id="received_or_sent_at" name="received_or_sent_at" value="{{ old('received_or_sent_at', date('Y-m-d\TH:i')) }}" required>
                        @error('received_or_sent_at')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="due_date" class="form-label fw-semibold small">Fecha Límite de Atención (Término)</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                        <small class="text-muted">Calculada automáticamente o ajustable según necesidad.</small>
                        @error('due_date')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="observations" class="form-label fw-semibold small">Observaciones Iniciales</label>
                    <textarea class="form-control" id="observations" name="observations" rows="2" placeholder="Notas de recepción...">{{ old('observations') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                    <i class="bi bi-file-earmark-check me-1"></i> Radicar Documento Oficial
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    const typeSelect = document.getElementById('document_type_id');
    const dueDateInput = document.getElementById('due_date');
    const senderCatalog = document.getElementById('sender_catalog');

    typeSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const days = parseInt(selected.getAttribute('data-days') || 0);

        if (days > 0) {
            const today = new Date();
            today.setDate(today.getDate() + days);
            dueDateInput.value = today.toISOString().split('T')[0];
        }
    });

    senderCatalog?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            document.getElementById('sender_name').value = selected.getAttribute('data-name');
            document.getElementById('sender_institution').value = selected.getAttribute('data-institution');
        }
    });
</script>
@endsection
