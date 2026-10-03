@extends('layouts.app')

@section('title', 'Búsqueda Avanzada de Correspondencia')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-search text-primary me-2"></i>Búsqueda Avanzada Global
        </h3>
        <p class="text-secondary mb-0">Localice correspondencia institucional combinando múltiples criterios, filtros y semáforos.</p>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="GET" action="{{ route('search.advanced') }}">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label small fw-semibold">Palabra Clave (Folio, Oficio Externo, Asunto, Remitente o Destinatario)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Escriba texto a buscar...">
                </div>
            </div>

            <div class="col-md-3">
                <label for="direction" class="form-label small fw-semibold">Flujo de Correspondencia</label>
                <select name="direction" id="direction" class="form-select">
                    <option value="">Cualquier flujo</option>
                    @foreach(App\Enums\DocumentDirection::cases() as $dir)
                        <option value="{{ $dir->value }}" {{ request('direction') === $dir->value ? 'selected' : '' }}>
                            {{ $dir->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="department_id" class="form-label small fw-semibold">Departamento Responsable</label>
                <select name="department_id" id="department_id" class="form-select">
                    <option value="">Todos los departamentos</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="document_type_id" class="form-label small fw-semibold">Tipo Documental</label>
                <select name="document_type_id" id="document_type_id" class="form-select">
                    <option value="">Todos los tipos</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="priority" class="form-label small fw-semibold">Prioridad</label>
                <select name="priority" id="priority" class="form-select">
                    <option value="">Todas las prioridades</option>
                    @foreach(App\Enums\Priority::cases() as $priority)
                        <option value="{{ $priority->value }}" {{ request('priority') === $priority->value ? 'selected' : '' }}>
                            {{ $priority->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="status" class="form-label small fw-semibold">Estado del Trámite</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Todos los estados</option>
                    @foreach(App\Enums\DocumentStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="traffic_light" class="form-label small fw-semibold">Semáforo de Cumplimiento</label>
                <select name="traffic_light" id="traffic_light" class="form-select">
                    <option value="">Cualquier condición</option>
                    <option value="green" {{ request('traffic_light') === 'green' ? 'selected' : '' }}>Al corriente (Verde)</option>
                    <option value="yellow" {{ request('traffic_light') === 'yellow' ? 'selected' : '' }}>Por vencer en 3 días (Amarillo)</option>
                    <option value="red" {{ request('traffic_light') === 'red' ? 'selected' : '' }}>Vencido (Rojo)</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="assigned_user_id" class="form-label small fw-semibold">Funcionario Asignado</label>
                <select name="assigned_user_id" id="assigned_user_id" class="form-select">
                    <option value="">Cualquier funcionario</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('assigned_user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="has_attachments" class="form-label small fw-semibold">Archivos Digitales</label>
                <select name="has_attachments" id="has_attachments" class="form-select">
                    <option value="">Indiferente</option>
                    <option value="1" {{ request('has_attachments') === '1' ? 'selected' : '' }}>Con adjuntos digitalizados</option>
                    <option value="0" {{ request('has_attachments') === '0' ? 'selected' : '' }}>Sin adjuntos</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="has_expedient" class="form-label small fw-semibold">Expediente Digital</label>
                <select name="has_expedient" id="has_expedient" class="form-select">
                    <option value="">Indiferente</option>
                    <option value="1" {{ request('has_expedient') === '1' ? 'selected' : '' }}>Integrado en expediente</option>
                    <option value="0" {{ request('has_expedient') === '0' ? 'selected' : '' }}>Sin expediente</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="date_from" class="form-label small fw-semibold">Fecha Documento Desde</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-3">
                <label for="date_to" class="form-label small fw-semibold">Fecha Documento Hasta</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('search.advanced') }}" class="btn btn-light border">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Restablecer Filtros
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-funnel-fill me-1"></i> Aplicar Búsqueda
            </button>
        </div>
    </form>
</div>

<div class="card card-custom overflow-hidden">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-list-check me-2 text-primary"></i>Resultados de la Búsqueda
        </h5>
        <span class="badge bg-primary px-3 py-2 fs-6">{{ $results->total() }} documento(s) encontrado(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary small text-uppercase">
                <tr>
                    <th class="ps-4">Folio Oficial</th>
                    <th>Asunto & Participantes</th>
                    <th>Tipo</th>
                    <th>Departamento & Responsable</th>
                    <th>Prioridad</th>
                    <th>Término</th>
                    <th>Estado</th>
                    <th>Adjuntos / Expediente</th>
                    <th class="text-end pe-4">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $doc)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-primary">{{ $doc->folio }}</span>
                            @if($doc->external_number)
                                <div class="text-muted small">{{ $doc->external_number }}</div>
                            @endif
                            <span class="badge {{ $doc->direction->badgeClass() }} py-0 px-1 mt-1">
                                {{ $doc->direction->label() }}
                            </span>
                        </td>
                        <td style="max-width: 280px;">
                            <div class="fw-semibold text-dark text-truncate" title="{{ $doc->subject }}">{{ $doc->subject }}</div>
                            <small class="text-muted text-truncate d-block">
                                {{ $doc->direction === App\Enums\DocumentDirection::RECEIVED ? 'De: ' . $doc->sender_name : 'Para: ' . $doc->recipient_name }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $doc->documentType->code }}</span>
                            <small class="d-block text-muted">{{ $doc->documentType->name }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $doc->department->name }}</div>
                            <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $doc->assignedUser?->name ?? 'Área general' }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $doc->priority->badgeClass() }}">
                                <i class="bi {{ $doc->priority->icon() }} me-1"></i> {{ $doc->priority->label() }}
                            </span>
                        </td>
                        <td>
                            @if($doc->due_date)
                                <div class="fw-semibold {{ $doc->traffic_light === 'red' ? 'text-danger' : ($doc->traffic_light === 'yellow' ? 'text-warning-emphasis' : 'text-success') }}">
                                    {{ $doc->due_date->format('d/m/Y') }}
                                </div>
                                <small>
                                    @if($doc->traffic_light === 'red')
                                        <span class="badge bg-danger-subtle text-danger">Vencido</span>
                                    @elseif($doc->traffic_light === 'yellow')
                                        <span class="badge bg-warning-subtle text-warning-emphasis">{{ $doc->days_remaining }} días</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">{{ $doc->days_remaining }} días</span>
                                    @endif
                                </small>
                            @else
                                <span class="text-muted small">Sin término</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $doc->status->badgeClass() }}">
                                {{ $doc->status->label() }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @if($doc->attachments->isNotEmpty())
                                    <span class="badge bg-info-subtle text-info border border-info-subtle py-1">
                                        <i class="bi bi-paperclip me-1"></i> {{ $doc->attachments->count() }} adjunto(s)
                                    </span>
                                @endif
                                @if($doc->expedients->isNotEmpty())
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle py-1">
                                        <i class="bi bi-folder-fill me-1 text-warning"></i> {{ $doc->expedients->first()->code }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('documents.show', $doc) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-folder2-open me-1"></i> Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-search fs-1 d-block mb-2 text-secondary"></i>
                            No se encontraron documentos que coincidan con los criterios de búsqueda especificados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($results->hasPages())
        <div class="card-footer bg-white border-top px-4 py-3">
            {{ $results->links() }}
        </div>
    @endif
</div>
@endsection
