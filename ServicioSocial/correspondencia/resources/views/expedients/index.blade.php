@extends('layouts.app')

@section('title', 'Expedientes Digitales')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-folder2-open text-primary me-2"></i>Expedientes Digitales
        </h3>
        <p class="text-secondary mb-0">Agrupación temático-institucional de correspondencia y documentos vinculados.</p>
    </div>
    @can('create', App\Models\Expedient::class)
        <a href="{{ route('expedients.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Expediente
        </a>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Expedientes</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($metrics['total']) }}</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-3">
                    <i class="bi bi-folder2 fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Abiertos</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($metrics['open']) }}</h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-3">
                    <i class="bi bi-folder-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">En Trámite</span>
                    <h3 class="fw-bold text-warning-emphasis mb-0 mt-1">{{ number_format($metrics['in_progress']) }}</h3>
                </div>
                <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-3">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-secondary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Cerrados</span>
                    <h3 class="fw-bold text-secondary mb-0 mt-1">{{ number_format($metrics['closed']) }}</h3>
                </div>
                <div class="bg-secondary-subtle text-secondary p-3 rounded-3">
                    <i class="bi bi-archive fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="GET" action="{{ route('expedients.index') }}" class="row g-3">
        <div class="col-md-5">
            <label for="search" class="form-label small fw-semibold">Buscar Código, Título o Descripción</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Código o título...">
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
            <label for="status" class="form-label small fw-semibold">Estado</label>
            <select name="status" id="status" class="form-select">
                <option value="">Todos los estados</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Abierto</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En Trámite</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Cerrado</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archivado</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel-fill me-1"></i> Filtrar
            </button>
            @if(request()->hasAny(['search', 'department_id', 'status']))
                <a href="{{ route('expedients.index') }}" class="btn btn-outline-secondary" title="Limpiar">
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
                    <th class="ps-4">Código</th>
                    <th>Título del Expediente</th>
                    <th>Departamento</th>
                    <th>Responsable</th>
                    <th>Documentos</th>
                    <th>Estado</th>
                    <th>Apertura</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expedients as $exp)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-primary">{{ $exp->code }}</span>
                        </td>
                        <td style="max-width: 300px;">
                            <div class="fw-semibold text-dark text-truncate" title="{{ $exp->title }}">{{ $exp->title }}</div>
                            @if($exp->description)
                                <small class="text-muted text-truncate d-block">{{ $exp->description }}</small>
                            @endif
                        </td>
                        <td>{{ $exp->department->name }}</td>
                        <td>
                            <small class="text-dark fw-semibold d-block">{{ $exp->responsibleUser?->name ?? 'Sin asignar' }}</small>
                            <small class="text-muted">{{ $exp->responsibleUser?->position ?? 'Responsable' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                <i class="bi bi-file-earmark-text me-1"></i> {{ $exp->documents_count }} doc(s)
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $exp->status_badge_class }}">
                                {{ $exp->status_label }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $exp->opened_at->format('d/m/Y') }}</small>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('expedients.show', $exp) }}" class="btn btn-outline-primary" title="Ver Expediente">
                                    <i class="bi bi-folder2-open me-1"></i> Ver
                                </a>
                                @can('update', $exp)
                                    <a href="{{ route('expedients.edit', $exp) }}" class="btn btn-outline-secondary" title="Editar Expediente">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                            No se encontraron expedientes digitales registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($expedients->hasPages())
        <div class="card-footer bg-white border-top px-4 py-3">
            {{ $expedients->links() }}
        </div>
    @endif
</div>
@endsection
