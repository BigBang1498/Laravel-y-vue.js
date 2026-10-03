@extends('layouts.app')

@section('title', 'Bitácora de Auditoría Forense')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-shield-check text-primary me-2"></i>Bitácora de Auditoría Forense
        </h3>
        <p class="text-secondary mb-0">Registro inmutable de trazabilidad de accesos, operaciones y modificaciones del sistema institucional</p>
    </div>
    <span class="badge bg-dark px-3 py-2 fs-6">
        <i class="bi bi-database-lock me-1"></i> Control Forense Activo
    </span>
</div>

<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('audit-logs.index') }}" class="row g-3 align-items-end">
        <div class="col-lg-3 col-md-6">
            <label class="form-label small fw-semibold text-muted mb-1">Buscar en descripción</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Término o palabra clave..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-semibold text-muted mb-1">Evento</label>
            <select name="event" class="form-select form-select-sm">
                <option value="">-- Todos los eventos --</option>
                @foreach($events as $eventKey => $eventLabel)
                    <option value="{{ $eventKey }}" {{ request('event') === $eventKey ? 'selected' : '' }}>{{ $eventLabel }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3 col-md-6">
            <label class="form-label small fw-semibold text-muted mb-1">Usuario</label>
            <select name="user_id" class="form-select form-select-sm">
                <option value="">-- Todos los usuarios --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (string)request('user_id') === (string)$user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6">
            <label class="form-label small fw-semibold text-muted mb-1">Fecha Desde</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6">
            <label class="form-label small fw-semibold text-muted mb-1">Fecha Hasta</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
            @if(request()->hasAny(['search', 'event', 'user_id', 'date_from', 'date_to']))
                <a href="{{ route('audit-logs.index') }}" class="btn btn-sm btn-outline-secondary px-3">
                    <i class="bi bi-x-circle me-1"></i> Limpiar Filtros
                </a>
            @endif
            <button type="submit" class="btn btn-sm btn-primary px-4">
                <i class="bi bi-funnel me-1"></i> Filtrar
            </button>
        </div>
    </form>
</div>

<div class="card card-custom overflow-hidden">
    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0">
            <i class="bi bi-list-check me-1 text-primary"></i> Registros de Auditoría ({{ number_format($logs->total()) }} eventos registrados)
        </h6>
        @if($logs->hasPages())
            <small class="text-muted">Página {{ $logs->currentPage() }} de {{ $logs->lastPage() }}</small>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3" style="width: 170px;">Fecha / Hora</th>
                    <th style="width: 200px;">Usuario</th>
                    <th style="width: 170px;">Evento</th>
                    <th>Descripción de la Operación</th>
                    <th class="text-end pe-3" style="width: 80px;">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold text-dark small">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                            <small class="text-muted" style="font-size: 0.72rem;">{{ $log->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            @if($log->user)
                                <div class="fw-semibold text-dark small">{{ $log->user->name }}</div>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $log->user->email }}</small>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Sistema / Invitado</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $log->eventBadgeClass() }} px-2 py-1">
                                <i class="bi {{ $log->eventIcon() }} me-1"></i> {{ $log->eventLabel() }}
                            </span>
                        </td>
                        <td>
                            <div class="small text-dark text-truncate-2">{{ $log->description }}</div>
                            @if($log->auditable_type)
                                <small class="text-muted font-monospace" style="font-size: 0.7rem;">
                                    {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                </small>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="Inspección forense">
                                <i class="bi bi-search"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-shield-x fs-1 mb-2 d-block text-secondary opacity-50"></i>
                            No se encontraron registros de auditoría que coincidan con los criterios seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="p-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="text-muted small">
                Mostrando del <strong>{{ $logs->firstItem() }}</strong> al <strong>{{ $logs->lastItem() }}</strong> de <strong>{{ number_format($logs->total()) }}</strong> registros
            </div>
            <div>
                {{ $logs->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
