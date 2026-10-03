@extends('layouts.app')

@section('title', 'Departamentos Universitarios')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold mb-1">Departamentos y Dependencias</h3>
        <p class="text-secondary mb-0">Estructura organizacional universitaria para radicación y asignación de correspondencia</p>
    </div>
    <a href="{{ route('departments.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-building-add me-1"></i> Registrar Departamento
    </a>
</div>

<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('departments.index') }}" class="row g-2 align-items-center">
        <div class="col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, sigla o descripción..." value="{{ request('search') }}">
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
                <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
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
                    <th class="ps-3">Sigla</th>
                    <th>Nombre de la Dependencia</th>
                    <th>Contacto (Teléfono / Correo)</th>
                    <th>Personal Asignado</th>
                    <th>Estado</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-primary-subtle text-primary font-monospace fs-6 px-2 py-1 border border-primary-subtle">
                                {{ $dept->code }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $dept->name }}</div>
                            <small class="text-muted">{{ Str::limit($dept->description, 60) ?? 'Sin descripción' }}</small>
                        </td>
                        <td>
                            <div class="small">
                                @if($dept->email)
                                    <div><i class="bi bi-envelope me-1 text-secondary"></i> {{ $dept->email }}</div>
                                @endif
                                @if($dept->phone)
                                    <div><i class="bi bi-telephone me-1 text-secondary"></i> {{ $dept->phone }}</div>
                                @endif
                                @if(!$dept->email && !$dept->phone)
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-people me-1"></i> {{ $dept->users_count }} funcionarios
                            </span>
                        </td>
                        <td>
                            @if($dept->is_active)
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
                                <a href="{{ route('departments.edit', $dept) }}" class="btn btn-outline-primary" title="Editar departamento">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('departments.toggle-status', $dept) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $dept->is_active ? 'warning' : 'success' }}" title="{{ $dept->is_active ? 'Desactivar' : 'Activar' }}">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                                <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar el departamento {{ $dept->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar" {{ $dept->users_count > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-building display-4 d-block mb-2 text-secondary"></i>
                            No se encontraron departamentos registrados con los criterios seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($departments->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $departments->links() }}
        </div>
    @endif
</div>
@endsection
