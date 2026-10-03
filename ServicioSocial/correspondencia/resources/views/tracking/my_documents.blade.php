@extends('layouts.app')

@section('title', 'Mis Documentos Asignados')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-person-check-fill text-primary me-2"></i>Mis Documentos Asignados
        </h3>
        <p class="text-secondary mb-0">Correspondencia institucional bajo su responsabilidad y atención técnica.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Asignados</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($metrics['total']) }}</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-3">
                    <i class="bi bi-folder-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">En Trámite</span>
                    <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($metrics['pending']) }}</h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-3">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Por Vencer (≤3 días)</span>
                    <h3 class="fw-bold text-warning-emphasis mb-0 mt-1">{{ number_format($metrics['due_soon']) }}</h3>
                </div>
                <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-3">
                    <i class="bi bi-alarm fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Vencidos</span>
                    <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($metrics['expired']) }}</h3>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-3">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="GET" action="{{ route('tracking.my-documents') }}" class="row g-3">
        <div class="col-md-5">
            <label for="search" class="form-label small fw-semibold">Buscar en Asunto, Folio, Remitente o Destinatario</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Escriba palabra clave o folio...">
            </div>
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
        <div class="col-md-2">
            <label for="status" class="form-label small fw-semibold">Estado</label>
            <select name="status" id="status" class="form-select">
                <option value="">Todos los estados</option>
                @foreach(App\Enums\DocumentStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel-fill me-1"></i> Filtrar
            </button>
            @if(request()->hasAny(['search', 'priority', 'status']))
                <a href="{{ route('tracking.my-documents') }}" class="btn btn-outline-secondary" title="Limpiar">
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
                    <th class="ps-4">Folio Oficial</th>
                    <th>Asunto & Entidad</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Fecha Límite</th>
                    <th>Estado</th>
                    <th>Turnado Por</th>
                    <th class="text-end pe-4">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-primary">{{ $doc->folio }}</span>
                            @if($doc->external_number)
                                <div class="text-muted small">{{ $doc->external_number }}</div>
                            @endif
                        </td>
                        <td style="max-width: 300px;">
                            <div class="fw-semibold text-dark text-truncate" title="{{ $doc->subject }}">{{ $doc->subject }}</div>
                            <small class="text-secondary text-truncate d-block">
                                @if($doc->direction === App\Enums\DocumentDirection::RECEIVED)
                                    <i class="bi bi-person-up me-1 text-primary"></i>De: {{ $doc->sender_name }} ({{ $doc->sender_institution ?? 'Particular' }})
                                @else
                                    <i class="bi bi-person-down me-1 text-success"></i>Para: {{ $doc->recipient_name }} ({{ $doc->recipient_institution ?? 'Particular' }})
                                @endif
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
                                    @elseif($doc->traffic_light === 'green')
                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-clock me-1"></i>{{ $doc->days_remaining }} días</span>
                                    @else
                                        <span class="badge bg-light text-muted">Finalizado</span>
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
                        <td>
                            <small class="text-dark fw-semibold d-block">{{ $doc->currentAssignment?->assigner?->name ?? 'Sistema' }}</small>
                            <small class="text-muted">{{ $doc->currentAssignment?->assigned_at?->format('d/m/Y H:i') ?? '-' }}</small>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('documents.show', $doc) }}" class="btn btn-outline-primary btn-sm" title="Ver Expediente">
                                <i class="bi bi-folder2-open me-1"></i> Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No tiene documentos asignados bajo los criterios seleccionados.
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
