@extends('layouts.app')

@section('title', 'Emitir Correspondencia Enviada')

@section('content')
<div class="mb-4">
    <a href="{{ route('sent-documents.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver a Enviados
    </a>
    <h3 class="fw-bold mb-1">Emitir Correspondencia Enviada (Salida)</h3>
    <p class="text-secondary mb-0">Registro y generación de folio oficial de salida institucional (UNI-SAL-{{ date('Y') }}-XXXXXX)</p>
</div>

<form action="{{ route('sent-documents.store') }}" method="POST">
    @csrf

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-send-fill text-success"></i>
                    Datos de la Emisión Oficial
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="external_number" class="form-label fw-semibold small">Número de Oficio Interno</label>
                        <input type="text" class="form-control font-monospace @error('external_number') is-invalid @enderror" id="external_number" name="external_number" value="{{ old('external_number') }}" placeholder="Ej. REC/2026/0150">
                        <small class="text-muted">Número o clave de oficio asignado por el área emisora.</small>
                        @error('external_number')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="document_type_id" class="form-label fw-semibold small">Tipo de Documento <span class="text-danger">*</span></label>
                        <select name="document_type_id" id="document_type_id" class="form-select @error('document_type_id') is-invalid @enderror" required>
                            <option value="">-- Seleccione tipo --</option>
                            @foreach($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_type_id')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label fw-semibold small">Asunto del Oficio de Salida <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="Síntesis de la comunicación oficial enviada...">
                    @error('subject')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold small">Extracto o Resumen del Contenido</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Extracto de acuerdos, respuesta otorgada o disposiciones enviadas...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <h5 class="fw-bold mb-3 border-top pt-3 d-flex align-items-center gap-2">
                    <i class="bi bi-person-lines-fill text-success"></i>
                    Emisor Universitario y Destinatario Externo
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="sender_name" class="form-label fw-semibold small">Funcionario Universitario Emisor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('sender_name') is-invalid @enderror" id="sender_name" name="sender_name" value="{{ old('sender_name', auth()->user()->name) }}" required>
                        @error('sender_name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="sender_institution" class="form-label fw-semibold small">Institución Emisora</label>
                        <input type="text" class="form-control @error('sender_institution') is-invalid @enderror" id="sender_institution" name="sender_institution" value="{{ old('sender_institution', config('app.name', 'Universidad')) }}">
                    </div>
                </div>

                <div class="mb-3 bg-light p-3 rounded-3 border">
                    <label for="recipient_catalog" class="form-label fw-semibold small mb-1">
                        <i class="bi bi-search me-1"></i> Autocompletar desde Catálogo de Destinatarios
                    </label>
                    <select id="recipient_catalog" class="form-select form-select-sm">
                        <option value="">-- Seleccionar destinatario registrado previamente (Opcional) --</option>
                        @foreach($recipients as $recipient)
                            <option value="{{ $recipient->id }}" data-name="{{ $recipient->name }}" data-institution="{{ $recipient->institution }}">
                                {{ $recipient->name }} — {{ $recipient->institution }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="recipient_name" class="form-label fw-semibold small">Nombre del Destinatario Externo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" required placeholder="Autoridad, funcionario o entidad externa">
                        @error('recipient_name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="recipient_institution" class="form-label fw-semibold small">Institución / Organismo Destino</label>
                        <input type="text" class="form-control @error('recipient_institution') is-invalid @enderror" id="recipient_institution" name="recipient_institution" value="{{ old('recipient_institution') }}" placeholder="Ej. SEP, CONAHCYT, Gobierno Estatal">
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
                    <i class="bi bi-sliders text-success"></i>
                    Parámetros de Despacho
                </h5>

                <div class="mb-3">
                    <label for="department_id" class="form-label fw-semibold small">Departamento Emisor <span class="text-danger">*</span></label>
                    <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                        <option value="">-- Seleccionar departamento --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (old('department_id', auth()->user()->department_id) == $dept->id) ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small d-block mb-2">Prioridad <span class="text-danger">*</span></label>
                    <div class="d-flex flex-column gap-2">
                        @foreach(App\Enums\Priority::cases() as $priority)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="priority" id="priority_sent_{{ $priority->value }}" value="{{ $priority->value }}"
                                    {{ old('priority', 'normal') === $priority->value ? 'checked' : '' }}>
                                <label class="form-check-label" for="priority_sent_{{ $priority->value }}">
                                    <span class="badge {{ $priority->badgeClass() }} px-2 py-1">
                                        <i class="bi {{ $priority->icon() }} me-1"></i> {{ $priority->label() }}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label for="security_level" class="form-label fw-semibold small">Nivel de Confidencialidad</label>
                    <select name="security_level" id="security_level" class="form-select" required>
                        <option value="public" {{ old('security_level', 'public') === 'public' ? 'selected' : '' }}>Público Institucional</option>
                        <option value="restricted" {{ old('security_level') === 'restricted' ? 'selected' : '' }}>Restringido</option>
                        <option value="confidential" {{ old('security_level') === 'confidential' ? 'selected' : '' }}>Confidencial</option>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <label for="document_date" class="form-label fw-semibold small">Fecha del Documento <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('document_date') is-invalid @enderror" id="document_date" name="document_date" value="{{ old('document_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-12">
                        <label for="received_or_sent_at" class="form-label fw-semibold small">Fecha y Hora de Emisión <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('received_or_sent_at') is-invalid @enderror" id="received_or_sent_at" name="received_or_sent_at" value="{{ old('received_or_sent_at', date('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="observations" class="form-label fw-semibold small">Observaciones de Envío</label>
                    <textarea class="form-control" id="observations" name="observations" rows="2" placeholder="Número de guía postal, mensajería, entrega en mano...">{{ old('observations') }}</textarea>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-semibold shadow-sm">
                    <i class="bi bi-send-check-fill me-1"></i> Emitir Documento de Salida
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    const recipientCatalog = document.getElementById('recipient_catalog');
    recipientCatalog?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            document.getElementById('recipient_name').value = selected.getAttribute('data-name');
            document.getElementById('recipient_institution').value = selected.getAttribute('data-institution');
        }
    });
</script>
@endsection
