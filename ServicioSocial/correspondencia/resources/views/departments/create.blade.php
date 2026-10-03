@extends('layouts.app')

@section('title', 'Registrar Departamento')

@section('content')
<div class="mb-4">
    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
    <h3 class="fw-bold mb-1">Registrar Nuevo Departamento</h3>
    <p class="text-secondary mb-0">Agregue una dependencia o facultad a la estructura universitaria</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label for="name" class="form-label fw-semibold small">Nombre del Departamento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Ej. Secretaría de Finanzas y Administración">
                        @error('name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="code" class="form-label fw-semibold small">Sigla / Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required placeholder="Ej. FIN">
                        @error('code')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold small">Descripción o Competencias del Área</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Funciones principales de la dependencia...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold small">Correo Electrónico Institucional</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="depto@universidad.edu">
                        @error('email')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold small">Teléfono / Conmutador / Extensión</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Ej. Ext. 3100">
                        @error('phone')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark" for="is_active">Departamento Activo</label>
                    </div>
                    <small class="text-muted">Los departamentos inactivos no aparecerán en las listas para asignación de nuevos documentos.</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('departments.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2 me-1"></i> Guardar Departamento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-custom p-4 bg-light border-0">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-1 text-primary"></i> Lineamientos</h6>
            <ul class="small text-secondary ps-3 mb-0">
                <li class="mb-2">La sigla identifica de forma única al departamento en folios y asignaciones.</li>
                <li class="mb-2">Una vez creado, podrá asignar funcionarios y jefes de departamento a esta dependencia.</li>
                <li class="mb-0">Los documentos de correspondencia recibida y enviada podrán turnarse a este departamento.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
