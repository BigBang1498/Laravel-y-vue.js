@extends('layouts.app')

@section('title', 'Reportes y Estadísticas')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>Centro de Reportes y Estadísticas
        </h3>
        <p class="text-secondary mb-0">Generación de informes consolidados de correspondencia y exportación en formatos oficiales</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('reports.export-csv', request()->all()) }}" class="btn btn-outline-success btn-sm px-3 shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar a Excel / CSV
        </a>
        <a href="{{ route('reports.export-pdf', request()->all()) }}" class="btn btn-outline-danger btn-sm px-3 shadow-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> Descargar Reporte PDF
        </a>
        <a href="{{ route('reports.print-summary', request()->all()) }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
            <i class="bi bi-printer me-1"></i> Vista para Imprimir
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="text-muted small fw-semibold text-uppercase">Total Filtrados</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-dark mb-0">{{ number_format($totalCount) }}</h3>
                <i class="bi bi-folder-check fs-3 text-primary"></i>
            </div>
            <small class="text-muted">Criterios actuales</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <div class="text-muted small fw-semibold text-uppercase">Recibidos</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-success mb-0">{{ number_format($receivedCount) }}</h3>
                <i class="bi bi-inbox-fill fs-3 text-success"></i>
            </div>
            <small class="text-muted">Entradas radicadas</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <div class="text-muted small fw-semibold text-uppercase">Enviados</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-info mb-0">{{ number_format($sentCount) }}</h3>
                <i class="bi bi-send-fill fs-3 text-info"></i>
            </div>
            <small class="text-muted">Salidas institucionales</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="text-muted small fw-semibold text-uppercase">En Trámite</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-warning-emphasis mb-0">{{ number_format($pendingCount) }}</h3>
                <i class="bi bi-clock-history fs-3 text-warning-emphasis"></i>
            </div>
            <small class="text-muted">Pendientes de atención</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="text-muted small fw-semibold text-uppercase">Vencidos</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-danger mb-0">{{ number_format($expiredCount) }}</h3>
                <i class="bi bi-exclamation-triangle-fill fs-3 text-danger"></i>
            </div>
            <small class="text-muted">Término expirado</small>
        </div>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-funnel text-primary"></i>
        Parámetros y Filtros del Reporte
    </h5>
    <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Sentido de Correspondencia</label>
            <select name="direction" class="form-select form-select-sm">
                <option value="">-- Todos los sentidos --</option>
                <option value="received" {{ request('direction') === 'received' ? 'selected' : '' }}>Recibida</option>
                <option value="sent" {{ request('direction') === 'sent' ? 'selected' : '' }}>Enviada</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Dependencia / Departamento</label>
            <select name="department_id" class="form-select form-select-sm">
                <option value="">-- Todos los departamentos --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ (string)request('department_id') === (string)$dept->id ? 'selected' : '' }}>
                        {{ $dept->name }} ({{ $dept->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Tipo de Documento</label>
            <select name="document_type_id" class="form-select form-select-sm">
                <option value="">-- Todos los tipos --</option>
                @foreach($documentTypes as $type)
                    <option value="{{ $type->id }}" {{ (string)request('document_type_id') === (string)$type->id ? 'selected' : '' }}>
                        {{ $type->name }} ({{ $type->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Estado de Trámite</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Todos los estados --</option>
                @foreach(App\Enums\DocumentStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Prioridad</label>
            <select name="priority" class="form-select form-select-sm">
                <option value="">-- Todas las prioridades --</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baja</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Responsable Asignado</label>
            <select name="assigned_to" class="form-select form-select-sm">
                <option value="">-- Todos los funcionarios --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (string)request('assigned_to') === (string)$user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Semáforo de Término</label>
            <select name="traffic_light" class="form-select form-select-sm">
                <option value="">-- Todos los semáforos --</option>
                <option value="green" {{ request('traffic_light') === 'green' ? 'selected' : '' }}>En Tiempo (>3 días)</option>
                <option value="yellow" {{ request('traffic_light') === 'yellow' ? 'selected' : '' }}>Por Vencer (≤3 días)</option>
                <option value="red" {{ request('traffic_light') === 'red' ? 'selected' : '' }}>Vencido</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Fecha Desde</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Fecha Hasta</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
            @if(request()->hasAny(['direction', 'department_id', 'document_type_id', 'status', 'priority', 'assigned_to', 'traffic_light', 'date_from', 'date_to']))
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Limpiar Filtros
                </a>
            @endif
            <button type="submit" class="btn btn-sm btn-primary px-4">
                <i class="bi bi-filter me-1"></i> Aplicar Filtros
            </button>
        </div>
    </form>
</div>

<div class="card card-custom overflow-hidden">
    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0">
            Registros Filtrados ({{ $documents->total() }} correspondencias encontradas)
        </h6>
        <small class="text-muted">Página {{ $documents->currentPage() }} de {{ $documents->lastPage() }}</small>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Folio Oficial</th>
                    <th>Tipo / Sentido</th>
                    <th>Remitente / Destinatario</th>
                    <th>Asunto Institucional</th>
                    <th>Dependencia</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Término</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('documents.show', $doc) }}" class="fw-bold font-monospace text-decoration-none">
                                {{ $doc->folio }}
                            </a>
                            <div class="text-muted small" style="font-size: 0.72rem;">
                                Radicado: {{ $doc->received_or_sent_at?->format('d/m/Y H:i') }}
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $doc->direction === App\Enums\DocumentDirection::RECEIVED ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-success-subtle text-success border border-success-subtle' }} px-2 py-1 mb-1 d-inline-block">
                                {{ $doc->direction->label() }}
                            </span>
                            <div class="small text-muted">{{ $doc->documentType?->name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">{{ $doc->direction === App\Enums\DocumentDirection::RECEIVED ? $doc->sender_name : $doc->recipient_name }}</div>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">
                                {{ $doc->direction === App\Enums\DocumentDirection::RECEIVED ? ($doc->sender_institution ?? 'Particular') : ($doc->recipient_institution ?? 'Institucional') }}
                            </small>
                        </td>
                        <td>
                            <div class="text-dark small text-truncate-2" style="max-width: 280px;">{{ $doc->subject }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $doc->department?->code }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $doc->priority->badgeClass() }} px-2 py-1">
                                {{ $doc->priority->label() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $doc->status->badgeClass() }} px-2 py-1">
                                {{ $doc->status->label() }}
                            </span>
                        </td>
                        <td>
                            @if($doc->due_date)
                                @php
                                    $tlClass = match($doc->traffic_light) {
                                        'red' => 'bg-danger text-white',
                                        'yellow' => 'bg-warning text-dark',
                                        'green' => 'bg-success text-white',
                                        default => 'bg-secondary text-white',
                                    };
                                @endphp
                                <span class="badge {{ $tlClass }} px-2 py-1 font-monospace">
                                    {{ $doc->due_date->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">Sin término</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-outline-primary" title="Ver Expediente">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('documents.slip', $doc) }}" class="btn btn-outline-secondary" title="Descargar Boleta de Radicación (PDF)">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                <a href="{{ route('documents.print-slip', $doc) }}" target="_blank" class="btn btn-outline-dark" title="Imprimir Boleta Oficial">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 mb-2 d-block text-secondary opacity-50"></i>
                            No se encontraron correspondencias que coincidan con los parámetros especificados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($documents->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $documents->links() }}
        </div>
    @endif
</div>
@endsection
