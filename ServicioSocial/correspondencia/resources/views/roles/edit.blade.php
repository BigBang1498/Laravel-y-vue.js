@extends('layouts.app')

@section('title', 'Editar Rol y Permisos')

@section('content')
<div class="mb-4">
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver a Roles
    </a>
    <h3 class="fw-bold mb-1">Editar Rol: {{ $role->label }}</h3>
    <p class="text-secondary mb-0">Modifique los datos principales del rol y ajuste su matriz de permisos</p>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> Por favor corrija los errores del formulario.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card card-custom p-4 mb-4">
        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-shield-check me-1 text-primary"></i> Información del Rol
        </h5>
        
        <div class="row g-3">
            <div class="col-md-6">
                <label for="label" class="form-label fw-semibold">Nombre del Rol <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $role->label) }}" required>
                @error('label')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="name" class="form-label fw-semibold">Identificador Técnico</label>
                <input type="text" class="form-control" id="name" value="{{ $role->name }}" disabled>
                <small class="text-muted">El identificador técnico no se puede modificar.</small>
            </div>

            <div class="col-12">
                <label for="description" class="form-label fw-semibold">Descripción</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description', $role->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <div class="form-check form-switch mt-1">
                    <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" role="switch" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $role->is_active) ? 'checked' : '' }} {{ $role->isProtected() ? 'disabled' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">
                        Rol Habilitado y Activo
                    </label>
                    @if($role->isProtected())
                        <input type="hidden" name="is_active" value="1">
                        <small class="text-muted d-block">El rol de Administrador principal no puede ser deshabilitado.</small>
                    @endif
                    @error('is_active')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom p-3 mb-4 bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong class="text-dark"><i class="bi bi-key me-1"></i> Matriz de Autorizaciones</strong>
                <div class="small text-muted">Seleccione los permisos asignados a este rol</div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllBtn">Marcar Todos</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">Desmarcar Todos</button>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach($allPermissions as $module => $permissions)
            <div class="col-lg-6">
                <div class="card card-custom h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h6 class="fw-bold text-primary text-uppercase mb-0">
                            <i class="bi bi-folder2 me-1"></i> {{ $module }}
                        </h6>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none py-0 select-module-btn" data-module="{{ Str::slug($module) }}">
                            Invertir
                        </button>
                    </div>

                    <div class="d-flex flex-column gap-2 module-group-{{ Str::slug($module) }}">
                        @foreach($permissions as $permission)
                            <div class="form-check">
                                <input class="form-check-input perm-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                    {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                    <strong class="text-dark">{{ $permission->label }}</strong>
                                    <span class="text-muted d-block" style="font-size: 0.78rem;">{{ $permission->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-end gap-2 pb-5">
        <a href="{{ route('roles.index') }}" class="btn btn-light border px-4">Cancelar</a>
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check2 me-1"></i> Guardar Cambios
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    document.getElementById('selectAllBtn')?.addEventListener('click', function() {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
    });

    document.getElementById('deselectAllBtn')?.addEventListener('click', function() {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
    });

    document.querySelectorAll('.select-module-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const moduleSlug = this.dataset.module;
            document.querySelectorAll(`.module-group-${moduleSlug} .perm-checkbox`).forEach(cb => {
                cb.checked = !cb.checked;
            });
        });
    });
</script>
@endsection
