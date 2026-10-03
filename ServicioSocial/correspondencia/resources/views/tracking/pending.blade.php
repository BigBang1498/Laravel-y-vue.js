@extends('layouts.app')

@section('title', 'Correspondencia en Trámite y Pendientes')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-clock-history text-primary me-2"></i>Correspondencia en Trámite y Pendientes
        </h3>
        <p class="text-secondary mb-0">Control integral de documentos pendientes de atención, resolución y respuesta.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Pendientes</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($metrics['total_pending']) }}</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-3">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Prioridad Urgente</span>
                    <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($metrics['urgent']) }}</h3>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-3">
                    <i class="bi bi-lightning-charge-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Prioridad Alta</span>
                    <h3 class="fw-bold text-warning-emphasis mb-0 mt-1">{{ number_format($metrics['high']) }}</h3>
                </div>
                <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-3">
                    <i class="bi bi-exclamation-circle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Por Vencer (≤3 días)</span>
                    <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($metrics['due_soon']) }}</h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-3">
                    <i class="bi bi-alarm fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="GET" action="{{ route('tracking.pending') }}" class="row g-3">
        <div class="col-md-4">
            <label for="search" class="form-label small fw-semibold">Buscar en Folio o Asunto</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Folio o asunto...">
            </div>
        </div>
        <div class="col-md-3">
            <label for="department_id" class="form-label small fw-semibold">Departamento</label>
            <select name="department_id" id="department_id" class="form-select">
                <option value="">Todos los departamentos</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="document_type_id" class="form-label small fw-semibold">Tipo Documento</label>
            <select name="document_type_id" id="document_type_id" class="form-select">
                <option value="">Todos los tipos</option>
                @foreach($documentTypes as $type)
                    <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="priority" class="form-label small fw-semibold">Prioridad</label>
            <select name="priority" id="priority" class="form-select">
                <option value="">Todas</option>
                @foreach(App\Enums\Priority::cases() as $priority)
                    <option value="{{ $priority->value }}" {{ request('priority') === $priority->value ? 'selected' : '' }}>
                        {{ $priority->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1 d-flex align-items-end gap-1">
            <button type="submit" class="btn btn-primary w-100" title="Filtrar">
                <i class="bi bi-funnel-fill"></i>
            </button>
            @if(request()->hasAny(['search', 'department_id', 'document_type_id', 'priority']))
                <a href="{{ route('tracking.pending') }}" class="btn btn-outline-secondary" title="Limpiar">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary small text-uppercase">
                <tr>
                    <th class="ps-4">Folio</th>
                    <th>Asunto Institucional</th>
                    <th>Departamento & Responsable</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Término / Semáforo</th>
                    <th>Estado</th>
                    <th class="text-end pe-4">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-primary">{{ $doc->folio }}</span>
                            <div class="text-muted small">
                                <span class="badge {{ $doc->direction->badgeClass() }} py-0 px-1">{{ $doc->direction->label() }}</span>
                            </div>
                        </td>
                        <td style="max-width: 320px;">
                            <div class="fw-semibold text-dark text-truncate" title="{{ $doc->subject }}">{{ $doc->subject }}</div>
                            <small class="text-secondary text-truncate d-block">
                                {{ $doc->direction === App\Enums\DocumentDirection::RECEIVED ? 'Remitente: ' . $doc->sender_name : 'Destinatario: ' . $doc->recipient_name }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $doc->department->name }}</div>
                            <small class="text-muted">
                                <i class="bi bi-person me-1"></i>{{ $doc->assignedUser?->name ?? 'Sin funcionario asignado' }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $doc->documentType->code }}</span>
                            <small class="d-block text-muted">{{ $doc->documentType->name }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $doc->priority->badgeClass() }}">
                                <i class="bi {{ $doc->priority->icon() }} me-1"></i>{{ $doc->priority->label() }}
                            </span>
                        </td>
                        <td>
                            @if($doc->due_date)
                                <div class="fw-semibold {{ $doc->traffic_light === 'red' ? 'text-danger' : ($doc->traffic_light === 'yellow' ? 'text-warning-emphasis' : 'text-success') }}">
                                    {{ $doc->due_date->format('d/m/Y') }}
                                </div>
                                <small>
                                    @if($doc->traffic_light === 'red')
                                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-alarm me-1"></i>Vencido</span>
                                    @elseif($doc->traffic_light === 'yellow')
                                        <span class="badge bg-warning-subtle text-warning-emphasis"><i class="bi bi-hourglass-split me-1"></i>{{ $doc->days_remaining }} días</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-clock me-1"></i>{{ $doc->days_remaining }} días</span>
                                    @endif
                                </small>
                            @else
                                <span class="text-muted small">Sin término</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $doc->status->badgeClass() }}">
                                <i class="bi {{ $doc->status->icon() }} me-1"></i>{{ $doc->status->label() }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('documents.show', $doc) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-folder2-open me-1"></i> Expediente
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-check2-circle fs-1 d-block mb-2 text-success"></i>
                            No hay trámites pendientes bajo los filtros especificados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($documents->hasPages())
        <div class="card-footer bg-white border-top px-4 py-3">
            {{ $documents->links() }}
        </div>
    @endif
</div>
@endsection
