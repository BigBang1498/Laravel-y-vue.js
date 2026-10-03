@extends('layouts.app')

@section('title', 'Detalle de Usuario')

@section('content')
<div class="mb-4">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div>
            <h3 class="fw-bold mb-1">{{ $user->name }}</h3>
            <p class="text-secondary mb-0">Detalles de la cuenta y permisos de acceso</p>
        </div>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Editar Usuario
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card card-custom p-4 text-center mb-4">
            <div class="mx-auto bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-2 mb-3" style="width: 80px; height: 80px;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-secondary small mb-2">{{ $user->email }}</p>

            <div class="mb-3">
                @if($user->is_active)
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                        <i class="bi bi-check-circle-fill me-1"></i> Cuenta Activa
                    </span>
                @else
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
                        <i class="bi bi-slash-circle-fill me-1"></i> Cuenta Inactiva
                    </span>
                @endif
            </div>

            <hr class="my-3">

            <div class="text-start small">
                <div class="mb-2">
                    <span class="text-muted d-block">Cargo Institucional:</span>
                    <strong>{{ $user->position ?? 'No especificado' }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Teléfono / Extensión:</span>
                    <strong>{{ $user->phone ?? 'No especificado' }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Departamento Adscrito:</span>
                    @if($user->department)
                        <strong class="text-primary">{{ $user->department->name }} ({{ $user->department->code }})</strong>
                    @else
                        <span class="text-muted">Sin departamento asignado</span>
                    @endif
                </div>
                <div class="mb-0">
                    <span class="text-muted d-block">Fecha de Alta:</span>
                    <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill text-primary"></i>
                Roles Asignados
            </h5>
            <div class="row g-2 mb-4">
                @forelse($user->roles as $role)
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="fw-bold text-dark mb-1">{{ $role->label }}</div>
                            <small class="text-secondary d-block">{{ $role->description }}</small>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning mb-0 small">Este usuario no tiene ningún rol asignado actualmente.</div>
                    </div>
                @endforelse
            </div>

            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-key-fill text-info"></i>
                Privilegios Operativos Efectivos
            </h5>
            @if($user->isAdmin())
                <div class="alert alert-success d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-star-fill text-warning"></i>
                    <span>Este usuario posee privilegios de <strong>Super Administrador</strong> con acceso total al sistema.</span>
                </div>
            @else
                @php
                    $permissions = $user->roles->pluck('permissions')->flatten()->unique('id')->groupBy('module');
                @endphp
                @forelse($permissions as $module => $modulePermissions)
                    <div class="mb-3">
                        <h6 class="fw-bold text-primary text-uppercase small mb-2">{{ $module }}</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($modulePermissions as $permission)
                                <span class="badge bg-light text-dark border px-2 py-1 small">
                                    <i class="bi bi-check2 text-success me-1"></i> {{ $permission->label }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No existen permisos asociados a los roles actuales de este usuario.</p>
                @endforelse
            @endif
        </div>
    </div>
</div>
@endsection
