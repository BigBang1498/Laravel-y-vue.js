@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="mb-4">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
    <h3 class="fw-bold mb-1">Editar Usuario: {{ $user->name }}</h3>
    <p class="text-secondary mb-0">Actualice la información personal, adscripción departamental o roles del funcionario</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold small">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold small">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="department_id" class="form-label fw-semibold small">Departamento o Dependencia</label>
                        <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror">
                            <option value="">-- Sin departamento asignado --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="position" class="form-label fw-semibold small">Cargo / Puesto Institucional</label>
                        <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $user->position) }}">
                        @error('position')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="phone" class="form-label fw-semibold small">Teléfono / Extensión</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="password" class="form-label fw-semibold small">Nueva Contraseña (Opcional)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Mínimo 8 caracteres">
                        @error('password')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="password_confirmation" class="form-label fw-semibold small">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repita la nueva contraseña">
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-4">
                    <label class="form-label fw-semibold small d-block mb-2">Asignación de Roles <span class="text-danger">*</span></label>
                    @error('roles')
                        <div class="text-danger small mb-2">{{ $message }}</div>
                    @enderror
                    <div class="row g-2">
                        @php $userRoleIds = old('roles', $user->roles->pluck('id')->toArray()); @endphp
                        @foreach($roles as $role)
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                                            {{ in_array($role->id, $userRoleIds) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark" for="role_{{ $role->id }}">
                                            {{ $role->label }}
                                        </label>
                                    </div>
                                    <p class="text-muted small mb-0 ps-4 mt-1">{{ $role->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                            {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                            {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                        <label class="form-check-label fw-semibold text-dark" for="is_active">Usuario Activo</label>
                    </div>
                    @if(auth()->id() === $user->id)
                        <small class="text-muted">No puede desactivar su propia cuenta en sesión.</small>
                    @else
                        <small class="text-muted">Si se desactiva, el usuario no podrá iniciar sesión en el sistema.</small>
                    @endif
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2 me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-custom p-4 bg-light border-0">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1 text-primary"></i> Metadatos de Registro</h6>
            <ul class="list-unstyled small text-secondary mb-0">
                <li class="mb-2"><strong>ID de Usuario:</strong> #{{ $user->id }}</li>
                <li class="mb-2"><strong>Departamento Actual:</strong> {{ $user->department?->name ?? 'Sin adscripción' }}</li>
                <li class="mb-2"><strong>Fecha de Registro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</li>
                <li class="mb-0"><strong>Última Modificación:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection
