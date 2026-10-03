@extends('layouts.app')

@section('title', 'Roles y Permisos')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold mb-1">Roles y Permisos Institucionales</h3>
        <p class="text-secondary mb-0">Configuración de perfiles de acceso, creación, estado y matriz de autorizaciones del sistema</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Rol
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-3">
    @foreach($roles as $role)
        <div class="col-md-6 col-xl-4">
            <div class="card card-custom h-100 p-4 d-flex flex-column justify-content-between {{ !$role->is_active ? 'bg-light border-dashed' : '' }}">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">{{ $role->label }}</h5>
                            <span class="badge bg-primary-subtle text-primary font-monospace">{{ $role->name }}</span>
                        </div>
                        <div>
                            @if($role->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle-fill me-1"></i>Activo</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-x-circle-fill me-1"></i>Deshabilitado</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-secondary small mb-3">{{ $role->description ?? 'Sin descripción.' }}</p>
                </div>

                <div>
                    <div class="d-flex justify-content-between text-muted small border-top pt-3 mb-3">
                        <div><i class="bi bi-people me-1"></i> <strong>{{ $role->users_count }}</strong> usuarios</div>
                        <div><i class="bi bi-key me-1"></i> <strong>{{ $role->permissions_count }}</strong> permisos</div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-primary btn-sm flex-grow-1" title="Configurar nombre, descripción y permisos">
                            <i class="bi bi-sliders me-1"></i> Editar
                        </a>

                        @if(!$role->isProtected())
                            <form action="{{ route('roles.toggle-status', $role) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $role->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                        title="{{ $role->is_active ? 'Deshabilitar rol' : 'Habilitar rol' }}">
                                    <i class="bi {{ $role->is_active ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                </button>
                            </form>

                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar el rol \'{{ $role->label }}\'? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar rol" {{ $role->users_count > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Modal para Crear Nuevo Rol -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="createRoleModalLabel">
                        <i class="bi bi-shield-plus me-1"></i> Crear Nuevo Rol Institucional
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="label" class="form-label fw-semibold">Nombre del Rol <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label') }}" placeholder="Ej. Auditor Interno" required>
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label for="name" class="form-label fw-semibold">Identificador Técnico <span class="text-muted">(Slug opcional)</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ej. auditor_interno">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Descripción del Rol</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" placeholder="Breve descripción de las funciones y responsabilidades del perfil">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Rol Activo e Inmediatamente Disponible</label>
                            </div>
                        </div>

                        <div class="col-12 border-top pt-3">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-key me-1 text-primary"></i> Permisos Iniciales (Opcional)</h6>
                            <p class="text-muted small mb-3">Puede asignar permisos en este momento o configurarlos después.</p>
                            
                            <div class="accordion" id="permissionsAccordion">
                                @foreach($allPermissions as $module => $permissions)
                                    <div class="accordion-item border rounded mb-2">
                                        <h2 class="accordion-header" id="heading-{{ Str::slug($module) }}">
                                            <button class="accordion-button collapsed py-2 text-primary fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($module) }}" aria-expanded="false" aria-controls="collapse-{{ Str::slug($module) }}">
                                                <i class="bi bi-folder2 me-2"></i> {{ $module }} ({{ count($permissions) }})
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ Str::slug($module) }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ Str::slug($module) }}" data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body p-3">
                                                <div class="row g-2">
                                                    @foreach($permissions as $permission)
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="modal_perm_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label small" for="modal_perm_{{ $permission->id }}">
                                                                    <strong>{{ $permission->label }}</strong>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2 me-1"></i> Crear Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
