@extends('layouts.app')

@section('title', 'Documentos Vencidos - Alerta de Términos')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Documentos Vencidos
        </h3>
        <p class="text-secondary mb-0">Atención urgente requerida para trámites cuyo término legal o administrativo ha expirado.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-4">
        <div class="card card-custom p-3 border-start border-4 border-danger bg-danger-subtle bg-opacity-10">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-danger small fw-semibold text-uppercase">Total Vencidos</span>
                    <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($metrics['total_expired']) }}</h3>
                </div>
                <div class="bg-danger text-white p-3 rounded-3">
                    <i class="bi bi-alarm-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Vencidos de Prioridad Urgente</span>
                    <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($metrics['urgent_expired']) }}</h3>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-3">
                    <i class="bi bi-lightning-charge-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="GET" action="{{ route('tracking.expired') }}" class="row g-3">
        <div class="col-md-6">
            <label for="search" class="form-label small fw-semibold">Buscar por Folio o Asunto</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Folio o asunto...">
            </div>
        </div>
        <div class="col-md-4">
            <label for="department_id" class="form-label small fw-semibold">Filtrar por Departamento</label>
            <select name="department_id" id="department_id" class="form-select">
                <option value="">Todos los departamentos</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-funnel-fill me-1"></i> Filtrar
            </button>
            @if(request()->hasAny(['search', 'department_id']))
                <a href="{{ route('tracking.expired') }}" class="btn btn-outline-secondary" title="Limpiar">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="card card-custom overflow-hidden border-danger-subtle">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-danger text-danger-emphasis small text-uppercase">
                <tr>
                    <th class="ps-4">Folio Oficial</th>
                    <th>Asunto Institucional</th>
                    <th>Departamento & Asignado</th>
                    <th>Prioridad</th>
                    <th>Fecha Límite</th>
                    <th>Días de Atraso</th>
                    <th>Estado</th>
                    <th class="text-end pe-4">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-danger">{{ $doc->folio }}</span>
                            @if($doc->external_number)
                                <div class="text-muted small">{{ $doc->external_number }}</div>
                            @endif
                        </td>
                        <td style="max-width: 320px;">
                            <div class="fw-semibold text-dark text-truncate" title="{{ $doc->subject }}">{{ $doc->subject }}</div>
                            <small class="text-muted d-block text-truncate">
                                {{ $doc->direction === App\Enums\DocumentDirection::RECEIVED ? 'De: ' . $doc->sender_name : 'Para: ' . $doc->recipient_name }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $doc->department->name }}</div>
                            <small class="text-muted">
                                <i class="bi bi-person me-1"></i>{{ $doc->assignedUser?->name ?? 'Sin asignar' }}
                            </small>
                        </td>
                        <td>
                            <span class="badge {{ $doc->priority->badgeClass() }}">
                                <i class="bi {{ $doc->priority->icon() }} me-1"></i>{{ $doc->priority->label() }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-danger">{{ $doc->due_date ? $doc->due_date->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td>
                            @php
                                $overdueDays = $doc->due_date ? (int) $doc->due_date->diffInDays(today()) : 0;
                            @endphp
                            <span class="badge bg-danger px-2 py-1">
                                <i class="bi bi-alarm me-1"></i>{{ $overdueDays }} días de atraso
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $doc->status->badgeClass() }}">
                                <i class="bi {{ $doc->status->icon() }} me-1"></i>{{ $doc->status->label() }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('documents.show', $doc) }}" class="btn btn-danger btn-sm">
                                <i class="bi bi-arrow-right-circle me-1"></i> Atender
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle-fill fs-1 d-block mb-2 text-success"></i>
                            Excelente: No se registran documentos con plazos vencidos en este momento.
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
