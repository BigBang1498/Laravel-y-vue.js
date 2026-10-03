@extends('layouts.app')

@section('title', 'Registrar Destinatario')

@section('content')
<div class="mb-4">
    <a href="{{ route('recipients.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al catálogo
    </a>
    <h3 class="fw-bold mb-1">Registrar Destinatario Frecuente</h3>
    <p class="text-secondary mb-0">Agregue un receptor de correspondencia externa al directorio institucional</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <form action="{{ route('recipients.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold small">Nombre del Destinatario <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Ej. Lic. Fernando Toledo Ortiz">
                        @error('name')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="institution" class="form-label fw-semibold small">Institución / Organismo de Destino <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('institution') is-invalid @enderror" id="institution" name="institution" value="{{ old('institution') }}" required placeholder="Ej. Consejo Estatal de Ciencia y Tecnología">
                        @error('institution')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="position" class="form-label fw-semibold small">Cargo del Destinatario</label>
                        <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position') }}" placeholder="Ej. Director General">
                        @error('position')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold small">Teléfono / Celular</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Ej. 33-8765-4321">
                        @error('phone')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small">Correo Electrónico</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="destinatario@organismo.org">
                    @error('email')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="address" class="form-label fw-semibold small">Dirección Física para Despacho Oficial</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Domicilio postal para envío físico de oficios...">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark" for="is_active">Destinatario Activo</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('recipients.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2 me-1"></i> Guardar Destinatario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
