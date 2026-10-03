@extends('layouts.app')

@section('title', 'Registrar Tipo de Documento')

@section('content')
<div class="mb-4">
    <a href="{{ route('document-types.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
    <h3 class="fw-bold mb-1">Registrar Nuevo Tipo de Documento</h3>
    <p class="text-secondary mb-0">Agregue un nuevo tipo de correspondencia oficial al catálogo configurable</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <form action="{{ route('document-types.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label for="name" class="form-label fw-semibold small">Nombre del Tipo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Ej. Oficio, Memorándum, Circular...">
                        @error('name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="code" class="form-label fw-semibold small">Código / Prefijo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required placeholder="Ej. OF, MEM, CIR...">
                        @error('code')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="default_days_to_expire" class="form-label fw-semibold small">Días Predeterminados de Vencimiento <span class="text-danger">*</span></label>
                    <div class="input-group" style="max-width: 250px;">
                        <input type="number" class="form-control @error('default_days_to_expire') is-invalid @enderror" id="default_days_to_expire" name="default_days_to_expire" value="{{ old('default_days_to_expire', 10) }}" min="1" max="365" required>
                        <span class="input-group-text bg-light text-muted">días hábiles</span>
                    </div>
                    <small class="text-muted">Se utilizará para calcular automáticamente la fecha límite al radicar correspondencia de este tipo.</small>
                    @error('default_days_to_expire')
                        <div class="invalid-feedback d-block small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold small">Descripción o Uso Normativo</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Descripción sobre cuándo debe usarse este tipo de documento...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark" for="is_active">Tipo de Documento Activo</label>
                    </div>
                    <small class="text-muted">Solo los tipos activos estarán disponibles en los formularios de radicación.</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('document-types.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2 me-1"></i> Guardar Tipo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-custom p-4 bg-light border-0">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-1 text-primary"></i> Ventajas del Catálogo</h6>
            <ul class="small text-secondary ps-3 mb-0">
                <li class="mb-2">Evita tener valores fijos escritos directamente en el código ("hardcodeados").</li>
                <li class="mb-2">Permite a la institución incorporar nuevos formatos documentales (ej. Pagarés, Cartas de intención, Dictámenes) sin alterar la base de código.</li>
                <li class="mb-0">El cálculo del semáforo de vencimiento tomará como base los días hábiles aquí configurados.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
