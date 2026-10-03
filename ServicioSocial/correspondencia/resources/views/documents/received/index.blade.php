@extends('layouts.app')

@section('title', 'Correspondencia Recibida')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold mb-1">Correspondencia Recibida (Entrada)</h3>
        <p class="text-secondary mb-0">Bandeja de radicación, control de términos y seguimiento de documentos entrantes</p>
    </div>
    @can('createReceived', App\Models\Document::class)
        <a href="{{ route('received-documents.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-file-earmark-plus-fill me-1"></i> Radicar Documento Recibido
        </a>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-secondary small fw-semibold">TOTAL RECIBIDOS</div>
                    <div class="fs-4 fw-bold text-dark">{{ $metrics['total'] }}</div>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-3 fs-4">
                    <i class="bi bi-inbox-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-secondary small fw-semibold">EN TRÁMITE / PENDIENTES</div>
                    <div class="fs-4 fw-bold text-dark">{{ $metrics['pending'] }}</div>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-3 fs-4">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-secondary small fw-semibold">URGENTES PENDIENTES</div>
                    <div class="fs-4 fw-bold text-danger">{{ $metrics['urgent'] }}</div>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-3 fs-4">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-secondary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-secondary small fw-semibold">VENCIDOS</div>
                    <div class="fs-4 fw-bold text-danger">{{ $metrics['expired'] }}</div>
                </div>
                <div class="bg-secondary-subtle text-secondary p-3 rounded-3 fs-4">
                    <i class="bi bi-alarm-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('received-documents.index') }}" class="row g-2">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Folio, oficio, asunto, remitente..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="department_id" class="form-select">
                <option value="">-- Departamento --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->code }} - {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="document_type_id" class="form-select">
                <option value="">-- Tipo Doc --</option>
                @foreach($documentTypes as $type)
                    <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="priority" class="form-select">
                <option value="">-- Prioridad --</option>
                @foreach(App\Enums\Priority::cases() as $priority)
                    <option value="{{ $priority->value }}" {{ request('priority') === $priority->value ? 'selected' : '' }}>
                        {{ $priority->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Estado --</option>
                @foreach(App\Enums\DocumentStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-secondary w-100" title="Filtrar">
                <i class="bi bi-funnel"></i>
            </button>
            @if(request()->hasAny(['search', 'department_id', 'document_type_id', 'priority', 'status', 'date_from', 'date_to']))
                <a href="{{ route('received-documents.index') }}" class="btn btn-outline-secondary" title="Limpiar">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Folio / No. Oficio</th>
                    <th>Asunto / Tipo</th>
                    <th>Remitente / Origen</th>
                    <th>Departamento Responsable</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Término / Semáforo</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('documents.show', $doc) }}" class="fw-bold font-monospace text-primary text-decoration-none d-block">
                                {{ $doc->folio }}
                            </a>
                            <small class="text-muted font-monospace">
                                {{ $doc->external_number ? 'Oficio: ' . $doc->external_number : 'S/N' }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark text-truncate" style="max-width: 260px;" title="{{ $doc->subject }}">
                                {{ $doc->subject }}
                            </div>
                            <span class="badge bg-light text-secondary border small">{{ $doc->documentType->name }}</span>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $doc->sender_name }}</div>
                            <small class="text-muted d-block">{{ $doc->sender_institution ?? 'Particular' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-building me-1 text-primary"></i> {{ $doc->department->name }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $doc->priority->badgeClass() }}">
                                <i class="bi {{ $doc->priority->icon() }} me-1"></i> {{ $doc->priority->label() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $doc->status->badgeClass() }}">
                                <i class="bi {{ $doc->status->icon() }} me-1"></i> {{ $doc->status->label() }}
                            </span>
                        </td>
                        <td>
                            @if($doc->due_date)
                                @php $traffic = $doc->traffic_light; $days = $doc->days_remaining; @endphp
                                @if($traffic === 'green')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-circle-fill me-1"></i> {{ $days }} días restantes
                                    </span>
                                @elseif($traffic === 'yellow')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Vence en {{ $days }} día(s)
                                    </span>
                                @elseif($traffic === 'red')
                                    <span class="badge bg-danger text-white fw-bold">
                                        <i class="bi bi-alarm-fill me-1"></i> Vencido ({{ abs($days) }} d)
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Concluido</span>
                                @endif
                                <small class="text-muted d-block" style="font-size: 0.72rem;">Límite: {{ $doc->due_date->format('d/m/Y') }}</small>
                            @else
                                <span class="badge bg-light text-muted border">Sin término</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-outline-primary" title="Ver Expediente">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('update', $doc)
                                    <a href="{{ route('received-documents.edit', $doc) }}" class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4 d-block mb-2 text-secondary"></i>
                            No se encontró correspondencia recibida registrada con los criterios indicados.
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
