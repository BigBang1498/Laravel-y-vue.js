@extends('layouts.app')

@section('title', 'Nuevo Expediente Digital')

@section('content')
<div class="mb-4">
    <a href="{{ route('expedients.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver a Expedientes
    </a>
    <h3 class="fw-bold text-dark mb-1">
        <i class="bi bi-folder-plus text-primary me-2"></i>Crear Nuevo Expediente Digital
    </h3>
    <p class="text-secondary mb-0">Genere un expediente para agrupar correspondencia institucional relacionada a un mismo trámite o proyecto.</p>
</div>

<div class="card card-custom p-4" style="max-width: 800px;">
    <form action="{{ route('expedients.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label fw-semibold small">Título o Nombre del Expediente <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="Ej: Convenio de Cooperación con ANUIES 2026" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-semibold small">Descripción o Justificación del Trámite</label>
            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Detalles de la gestión institucional, antecedentes o alcance del expediente...">{{ old('description') }}</textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="department_id" class="form-label fw-semibold small">Departamento Responsable <span class="text-danger">*</span></label>
                <select name="department_id" id="department_id" class="form-select" required>
                    <option value="">-- Seleccione Departamento --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', auth()->user()->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="responsible_user_id" class="form-label fw-semibold small">Funcionario Responsable</label>
                <select name="responsible_user_id" id="responsible_user_id" class="form-select">
                    <option value="">-- Asignar al creador / área general --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('responsible_user_id', auth()->id()) == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->position ?? 'Funcionario' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-4" style="max-width: 300px;">
            <label for="opened_at" class="form-label fw-semibold small">Fecha de Apertura <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="opened_at" name="opened_at" value="{{ old('opened_at', date('Y-m-d')) }}" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i> Generar Expediente Digital
            </button>
            <a href="{{ route('expedients.index') }}" class="btn btn-light border">Cancelar</a>
        </div>
    </form>
</div>
@endsection
