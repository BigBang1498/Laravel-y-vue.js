@extends('layouts.app')

@section('title', 'Panel de Control y Analítica')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-speedometer2 text-primary me-2"></i>Panel de Control y Analítica
        </h3>
        <p class="text-secondary mb-0">Bienvenido(a), <span class="fw-semibold text-dark">{{ $user->name }}</span> &bull; {{ $user->department?->name ?? 'Universidad' }}</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="badge bg-primary px-3 py-2 fs-6">
            <i class="bi bi-shield-check me-1"></i> {{ $user->roles->first()?->label ?? 'Usuario' }}
        </span>
        @if($user->position)
            <span class="badge bg-light text-dark border px-3 py-2 fs-6">
                <i class="bi bi-briefcase me-1"></i> {{ $user->position }}
            </span>
        @endif
        <a href="{{ route('search.advanced') }}" class="btn btn-outline-primary btn-sm px-3">
            <i class="bi bi-search me-1"></i> Búsqueda Global
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-2">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="text-muted small fw-semibold text-uppercase">Recibidos</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-dark mb-0">{{ number_format($metrics['received']) }}</h3>
                <i class="bi bi-inbox-fill fs-3 text-primary"></i>
            </div>
            <small class="text-muted"><a href="{{ route('received-documents.index') }}" class="text-decoration-none">Ver recibidos &rarr;</a></small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <div class="text-muted small fw-semibold text-uppercase">Enviados</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-success mb-0">{{ number_format($metrics['sent']) }}</h3>
                <i class="bi bi-send-fill fs-3 text-success"></i>
            </div>
            <small class="text-muted"><a href="{{ route('sent-documents.index') }}" class="text-decoration-none text-success">Ver enviados &rarr;</a></small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <div class="text-muted small fw-semibold text-uppercase">En Trámite</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-info mb-0">{{ number_format($metrics['pending']) }}</h3>
                <i class="bi bi-clock-history fs-3 text-info"></i>
            </div>
            <small class="text-muted"><a href="{{ route('tracking.pending') }}" class="text-decoration-none text-info">Ver pendientes &rarr;</a></small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="text-muted small fw-semibold text-uppercase">Por Vencer</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-warning-emphasis mb-0">{{ number_format($metrics['due_soon']) }}</h3>
                <i class="bi bi-alarm fs-3 text-warning-emphasis"></i>
            </div>
            <small class="text-muted"><span class="text-warning-emphasis fw-semibold">≤ 3 días término</span></small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="text-muted small fw-semibold text-uppercase">Vencidos</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-danger mb-0">{{ number_format($metrics['expired']) }}</h3>
                <i class="bi bi-exclamation-triangle-fill fs-3 text-danger"></i>
            </div>
            <small class="text-muted"><a href="{{ route('tracking.expired') }}" class="text-decoration-none text-danger">Ver vencidos &rarr;</a></small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card card-custom p-3 border-start border-4 border-dark">
            <div class="text-muted small fw-semibold text-uppercase">Expedientes</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <h3 class="fw-bold text-dark mb-0">{{ number_format($metrics['expedients']) }}</h3>
                <i class="bi bi-folder2-open fs-3 text-dark"></i>
            </div>
            <small class="text-muted"><a href="{{ route('expedients.index') }}" class="text-decoration-none text-dark">Ver expedientes &rarr;</a></small>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                Accesos Directos y Operación Rápida
            </h5>
            <div class="row g-3">
                @can('createReceived', App\Models\Document::class)
                    <div class="col-sm-6 col-md-3">
                        <a href="{{ route('received-documents.create') }}" class="btn btn-outline-primary w-100 p-3 text-center rounded-3 d-flex flex-column align-items-center gap-2 h-100">
                            <i class="bi bi-inbox-fill fs-2"></i>
                            <span class="fw-semibold small">Radicar Recibida</span>
                        </a>
                    </div>
                @endcan

                @can('createSent', App\Models\Document::class)
                    <div class="col-sm-6 col-md-3">
                        <a href="{{ route('sent-documents.create') }}" class="btn btn-outline-success w-100 p-3 text-center rounded-3 d-flex flex-column align-items-center gap-2 h-100">
                            <i class="bi bi-send-fill fs-2"></i>
                            <span class="fw-semibold small">Radicar Salida</span>
                        </a>
                    </div>
                @endcan

                <div class="col-sm-6 col-md-3">
                    <a href="{{ route('tracking.my-documents') }}" class="btn btn-outline-info w-100 p-3 text-center rounded-3 d-flex flex-column align-items-center gap-2 h-100">
                        <i class="bi bi-person-check-fill fs-2"></i>
                        <span class="fw-semibold small">Mis Asignaciones</span>
                    </a>
                </div>

                <div class="col-sm-6 col-md-3">
                    <a href="{{ route('search.advanced') }}" class="btn btn-outline-secondary w-100 p-3 text-center rounded-3 d-flex flex-column align-items-center gap-2 h-100">
                        <i class="bi bi-funnel-fill fs-2"></i>
                        <span class="fw-semibold small">Búsqueda Global</span>
                    </a>
                </div>
            </div>
        </div>

        @if($myPendingDocuments->isNotEmpty())
            <div class="card card-custom overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-clock me-1"></i> Mis Documentos Pendientes de Atención
                    </h6>
                    <a href="{{ route('tracking.my-documents') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light text-uppercase">
                            <tr>
                                <th class="ps-3">Folio</th>
                                <th>Asunto</th>
                                <th>Prioridad</th>
                                <th>Término</th>
                                <th class="text-end pe-3">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myPendingDocuments as $myDoc)
                                <tr>
                                    <td class="ps-3 fw-bold font-monospace text-primary">{{ $myDoc->folio }}</td>
                                    <td style="max-width: 250px;" class="text-truncate">{{ $myDoc->subject }}</td>
                                    <td>
                                        <span class="badge {{ $myDoc->priority->badgeClass() }}">{{ $myDoc->priority->label() }}</span>
                                    </td>
                                    <td>
                                        @if($myDoc->due_date)
                                            <span class="{{ $myDoc->traffic_light === 'red' ? 'text-danger fw-bold' : ($myDoc->traffic_light === 'yellow' ? 'text-warning-emphasis fw-bold' : 'text-success') }}">
                                                {{ $myDoc->due_date->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin término</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('documents.show', $myDoc) }}" class="btn btn-sm btn-outline-primary py-0">Atender</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="card card-custom overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text-fill text-primary"></i>
                    Correspondencia Reciente
                </h5>
                <a href="{{ route('received-documents.index') }}" class="btn btn-sm btn-outline-secondary">Ver Bandeja Completa</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4">Folio</th>
                            <th>Asunto</th>
                            <th>Departamento</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDocuments as $doc)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold font-monospace text-primary">{{ $doc->folio }}</span>
                                    <div class="text-muted small">
                                        <span class="badge {{ $doc->direction->badgeClass() }} py-0 px-1">{{ $doc->direction->label() }}</span>
                                    </div>
                                </td>
                                <td style="max-width: 280px;">
                                    <div class="fw-semibold text-dark text-truncate" title="{{ $doc->subject }}">{{ $doc->subject }}</div>
                                    <small class="text-muted text-truncate d-block">{{ $doc->documentType->name }}</small>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $doc->department->name }}</div>
                                    <small class="text-muted">{{ $doc->assignedUser?->name ?? 'Área General' }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $doc->priority->badgeClass() }}">
                                        {{ $doc->priority->label() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $doc->status->badgeClass() }}">
                                        {{ $doc->status->label() }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('documents.show', $doc) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-folder2-open"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No se registran documentos en el sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($departmentStaffWorkload->isNotEmpty())
            <div class="card card-custom overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-people-fill text-primary me-1"></i> Carga de Trabajo de Funcionarios (Trámites Activos)
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light text-uppercase">
                            <tr>
                                <th class="ps-3">Funcionario</th>
                                <th>Cargo</th>
                                <th>Correo Institucional</th>
                                <th class="text-end pe-3">Documentos Pendientes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departmentStaffWorkload as $staff)
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">{{ $staff->name }}</td>
                                    <td>{{ $staff->position ?? 'Funcionario' }}</td>
                                    <td class="text-muted">{{ $staff->email }}</td>
                                    <td class="text-end pe-3">
                                        <span class="badge {{ $staff->pending_count > 0 ? 'bg-primary' : 'bg-secondary-subtle text-secondary' }} px-3 py-1">
                                            {{ $staff->pending_count }} activo(s)
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart-fill text-primary"></i>
                Distribución por Prioridad
            </h5>
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="d-flex justify-content-between small fw-semibold mb-1">
                        <span class="text-danger"><i class="bi bi-lightning-charge-fill me-1"></i> Urgente</span>
                        <span>{{ $priorityBreakdown['urgent'] }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: {{ $metrics['pending'] > 0 ? ($priorityBreakdown['urgent'] / $metrics['pending']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between small fw-semibold mb-1">
                        <span class="text-warning-emphasis"><i class="bi bi-exclamation-circle-fill me-1"></i> Alta</span>
                        <span>{{ $priorityBreakdown['high'] }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ $metrics['pending'] > 0 ? ($priorityBreakdown['high'] / $metrics['pending']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between small fw-semibold mb-1">
                        <span class="text-primary"><i class="bi bi-info-circle-fill me-1"></i> Normal</span>
                        <span>{{ $priorityBreakdown['normal'] }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ $metrics['pending'] > 0 ? ($priorityBreakdown['normal'] / $metrics['pending']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between small fw-semibold mb-1">
                        <span class="text-secondary"><i class="bi bi-arrow-down-circle-fill me-1"></i> Baja</span>
                        <span>{{ $priorityBreakdown['low'] }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-secondary" style="width: {{ $metrics['pending'] > 0 ? ($priorityBreakdown['low'] / $metrics['pending']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        @if($departmentBreakdown->isNotEmpty() && $user->isAdmin())
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-building text-primary"></i>
                    Volumen por Dependencia
                </h5>
                <ul class="list-group list-group-flush small">
                    @foreach($departmentBreakdown as $dept)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <span class="fw-semibold text-dark">{{ $dept->name }}</span>
                                <small class="text-muted d-block font-monospace">{{ $dept->code }}</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                {{ $dept->total_docs }} doc(s)
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i>
                Últimos Movimientos en Bitácora
            </h5>
            <div class="d-flex flex-column gap-3 small">
                @forelse($recentHistory as $hist)
                    <div class="border-bottom pb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-dark-subtle text-dark px-2 py-0" style="font-size: 0.7rem;">{{ $hist->action }}</span>
                            <small class="text-muted">{{ $hist->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="fw-semibold text-dark text-truncate">{{ $hist->document->folio }}</div>
                        <p class="mb-0 text-secondary text-truncate">{{ $hist->description }}</p>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Sin actividad registrada en bitácora.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
