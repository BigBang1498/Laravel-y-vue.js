@extends('layouts.app')

@section('title', 'Remitentes Externos')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold mb-1">Catálogo de Remitentes</h3>
        <p class="text-secondary mb-0">Instituciones, organismos y personas de origen de la correspondencia recibida</p>
    </div>
    <a href="{{ route('senders.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Registrar Remitente
    </a>
</div>

<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('senders.index') }}" class="row g-2 align-items-center">
        <div class="col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, institución, cargo o correo..." value="{{ request('search') }}">
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
                <a href="{{ route('senders.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
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
                    <th class="ps-3">Remitente</th>
                    <th>Institución / Organismo</th>
                    <th>Contacto (Correo / Teléfono)</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($senders as $sender)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold text-dark">{{ $sender->name }}</div>
                            <small class="text-muted">{{ $sender->position ?? 'Particular / Sin cargo' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $sender->institution }}</span>
                        </td>
                        <td>
                            <div class="small">
                                @if($sender->email)
                                    <div><i class="bi bi-envelope me-1 text-secondary"></i> {{ $sender->email }}</div>
                                @endif
                                @if($sender->phone)
                                    <div><i class="bi bi-telephone me-1 text-secondary"></i> {{ $sender->phone }}</div>
                                @endif
                                @if(!$sender->email && !$sender->phone)
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit($sender->address, 50) ?? '—' }}</small>
                        </td>
                        <td>
                            @if($sender->is_active)
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
                                <a href="{{ route('senders.edit', $sender) }}" class="btn btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('senders.destroy', $sender) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar al remitente {{ $sender->name }}?');">
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
                            <i class="bi bi-person-lines-fill display-4 d-block mb-2 text-secondary"></i>
                            No se encontraron remitentes registrados en el catálogo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($senders->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $senders->links() }}
        </div>
    @endif
</div>
@endsection
