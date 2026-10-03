@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold mb-1">Usuarios del Sistema</h3>
        <p class="text-secondary mb-0">Administración de funcionarios, adscripción departamental y asignación de roles institucionales</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Registrar Usuario
    </a>
</div>

<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, correo o cargo..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="department_id" class="form-select">
                <option value="">-- Todos los departamentos --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }} ({{ $dept->code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="role_id" class="form-select">
                <option value="">-- Todos los roles --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Estados --</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activos</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-secondary w-100" title="Aplicar filtros">
                <i class="bi bi-funnel"></i>
            </button>
            @if(request()->hasAny(['search', 'role_id', 'department_id', 'status']))
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
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
                    <th class="ps-3">Funcionario</th>
                    <th>Correo</th>
                    <th>Departamento Adscrito</th>
                    <th>Cargo / Puesto</th>
                    <th>Roles</th>
                    <th>Estado</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light text-primary rounded-circle border d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                    <small class="text-muted">ID: #{{ $user->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->department)
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-building me-1 text-primary"></i> {{ $user->department->name }}
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">Sin Departamento</span>
                            @endif
                        </td>
                        <td>{{ $user->position ?? '—' }}</td>
                        <td>
                            @forelse($user->roles as $role)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">
                                    {{ $role->label }}
                                </span>
                            @empty
                                <span class="badge bg-secondary-subtle text-secondary">Sin Rol</span>
                            @endforelse
                        </td>
                        <td>
                            @if($user->is_active)
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
                                <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary" title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary" title="Editar usuario">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" title="{{ $user->is_active ? 'Desactivar usuario' : 'Activar usuario' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar al usuario {{ $user->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar usuario">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people display-4 d-block mb-2 text-secondary"></i>
                            No se encontraron usuarios registrados con los criterios seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
