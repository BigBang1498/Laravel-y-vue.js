@extends('layouts.app')

@section('title', 'Detalle de Auditoría #' . $auditLog->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i> Volver a la Bitácora
    </a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-shield-shaded text-primary me-2"></i>Inspección Forense de Evento #{{ $auditLog->id }}
            </h3>
            <p class="text-secondary mb-0">Detalle técnico inmutable del registro de auditoría institucional</p>
        </div>
        <span class="badge {{ $auditLog->eventBadgeClass() }} px-3 py-2 fs-6">
            <i class="bi {{ $auditLog->eventIcon() }} me-1"></i> {{ $auditLog->eventLabel() }}
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i>
                Datos del Evento y Contexto
            </h5>
            <dl class="row mb-0 small">
                <dt class="col-sm-4 text-muted">ID de Registro:</dt>
                <dd class="col-sm-8 fw-semibold font-monospace">#{{ $auditLog->id }}</dd>

                <dt class="col-sm-4 text-muted">Fecha y Hora:</dt>
                <dd class="col-sm-8 fw-semibold">{{ $auditLog->created_at->format('d/m/Y H:i:s') }} ({{ $auditLog->created_at->diffForHumans() }})</dd>

                <dt class="col-sm-4 text-muted">Tipo de Evento:</dt>
                <dd class="col-sm-8 font-monospace">{{ $auditLog->event }}</dd>

                <dt class="col-sm-4 text-muted">Dirección IP:</dt>
                <dd class="col-sm-8 font-monospace">{{ $auditLog->ip_address ?? '127.0.0.1' }}</dd>

                <dt class="col-sm-4 text-muted">Navegador / Cliente:</dt>
                <dd class="col-sm-8 text-break text-muted font-monospace" style="font-size: 0.75rem;">
                    {{ $auditLog->user_agent ?? 'No disponible' }}
                </dd>

                <dt class="col-sm-4 text-muted">Entidad Afectada:</dt>
                <dd class="col-sm-8 font-monospace">
                    @if($auditLog->auditable_type)
                        {{ class_basename($auditLog->auditable_type) }} #{{ $auditLog->auditable_id }}
                    @else
                        <span class="text-muted">Operación General del Sistema</span>
                    @endif
                </dd>

                <dt class="col-sm-4 text-muted">Descripción:</dt>
                <dd class="col-sm-8 fw-medium text-dark">{{ $auditLog->description }}</dd>
            </dl>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person-badge text-primary"></i>
                Usuario Responsable
            </h5>
            @if($auditLog->user)
                <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-3 border">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fs-4 fw-bold" style="width: 50px; height: 50px;">
                        {{ strtoupper(substr($auditLog->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">{{ $auditLog->user->name }}</div>
                        <div class="text-muted small">{{ $auditLog->user->email }}</div>
                    </div>
                </div>

                <dl class="row mb-0 small">
                    <dt class="col-sm-4 text-muted">Rol en Sistema:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            {{ $auditLog->user->roles->first()?->label ?? 'Sin Rol' }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 text-muted">Departamento:</dt>
                    <dd class="col-sm-8">{{ $auditLog->user->department?->name ?? 'Sin adscripción' }}</dd>

                    <dt class="col-sm-4 text-muted">Puesto:</dt>
                    <dd class="col-sm-8">{{ $auditLog->user->position ?? 'No especificado' }}</dd>

                    <dt class="col-sm-4 text-muted">Estado de Cuenta:</dt>
                    <dd class="col-sm-8">
                        @if($auditLog->user->isActive())
                            <span class="badge bg-success-subtle text-success">Activo</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">Inactivo</span>
                        @endif
                    </dd>
                </dl>
            @else
                <div class="p-4 text-center text-muted bg-light rounded-3 border">
                    <i class="bi bi-person-x fs-2 mb-2 d-block text-secondary opacity-50"></i>
                    <p class="mb-0 small">Operación no autenticada o ejecutada por el sistema.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($auditLog->old_values || $auditLog->new_values)
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-diff text-primary"></i>
        Comparación de Atributos Modificados
    </h5>
    @php
        $allKeys = array_unique(array_merge(
            array_keys($auditLog->old_values ?? []),
            array_keys($auditLog->new_values ?? [])
        ));
    @endphp

    <div class="table-responsive">
        <table class="table table-bordered align-middle small mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 25%;">Atributo</th>
                    <th style="width: 37.5%;" class="text-danger">Valor Previo (Old)</th>
                    <th style="width: 37.5%;" class="text-success">Valor Modificado (New)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allKeys as $key)
                    @php
                        $oldVal = $auditLog->old_values[$key] ?? null;
                        $newVal = $auditLog->new_values[$key] ?? null;
                        $isChanged = $oldVal !== $newVal;
                    @endphp
                    <tr class="{{ $isChanged ? 'table-warning bg-opacity-25' : '' }}">
                        <td class="fw-semibold font-monospace">{{ $key }}</td>
                        <td class="font-monospace text-break {{ $isChanged ? 'text-danger' : 'text-muted' }}">
                            {{ is_array($oldVal) ? json_encode($oldVal, JSON_UNESCAPED_UNICODE) : ($oldVal ?? 'null') }}
                        </td>
                        <td class="font-monospace text-break {{ $isChanged ? 'text-success fw-bold' : 'text-muted' }}">
                            {{ is_array($newVal) ? json_encode($newVal, JSON_UNESCAPED_UNICODE) : ($newVal ?? 'null') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
