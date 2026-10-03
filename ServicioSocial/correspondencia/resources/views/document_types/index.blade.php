@extends('layouts.app')

@section('title', 'Tipos de Documentos')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold mb-1">Tipos de Documentos</h3>
        <p class="text-secondary mb-0">Catálogo institucional configurable para clasificación de correspondencia (Oficio, Memorándum, Circular, etc.)</p>
    </div>
    <a href="{{ route('document-types.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-file-earmark-plus-fill me-1"></i> Registrar Tipo de Documento
    </a>
</div>

<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('document-types.index') }}" class="row g-2 align-items-center">
        <div class="col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, código o descripción..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Todos los estados --</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activos</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="bi bi-funnel"></i> Filtrar
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('document-types.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
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
                    <th class="ps-3">Código</th>
                    <th>Tipo de Documento</th>
                    <th>Término Predeterminado</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documentTypes as $type)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-secondary-subtle text-secondary font-monospace fs-6 px-2 py-1 border">
                                {{ $type->code }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $type->name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-calendar-event me-1 text-primary"></i> {{ $type->default_days_to_expire }} días hábiles
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $type->description ?? 'Sin descripción' }}</small>
                        </td>
                        <td>
                            @if($type->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Activo
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-slash-circle-fill me-1"></i> Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('document-types.edit', $type) }}" class="btn btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('document-types.toggle-status', $type) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $type->is_active ? 'warning' : 'success' }}" title="{{ $type->is_active ? 'Desactivar' : 'Activar' }}">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                                <form action="{{ route('document-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar el tipo de documento {{ $type->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-text display-4 d-block mb-2 text-secondary"></i>
                            No se encontraron tipos de documentos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($documentTypes->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $documentTypes->links() }}
        </div>
    @endif
</div>
@endsection
