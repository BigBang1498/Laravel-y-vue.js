@extends('layouts.app')

@section('title', 'Editar Expediente: ' . $expedient->code)

@section('content')
<div class="mb-4">
    <a href="{{ route('expedients.show', $expedient) }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al Expediente
    </a>
    <h3 class="fw-bold text-dark mb-1">
        <i class="bi bi-pencil-square text-primary me-2"></i>Editar Expediente: {{ $expedient->code }}
    </h3>
</div>

<div class="card card-custom p-4" style="max-width: 800px;">
    <form action="{{ route('expedients.update', $expedient) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label fw-semibold small">Título o Nombre del Expediente <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $expedient->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-semibold small">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $expedient->description) }}</textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="department_id" class="form-label fw-semibold small">Departamento Responsable <span class="text-danger">*</span></label>
                <select name="department_id" id="department_id" class="form-select" required>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $expedient->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="responsible_user_id" class="form-label fw-semibold small">Funcionario Responsable</label>
                <select name="responsible_user_id" id="responsible_user_id" class="form-select">
                    <option value="">-- Sin asignar / Área General --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('responsible_user_id', $expedient->responsible_user_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->position ?? 'Funcionario' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="status" class="form-label fw-semibold small">Estado del Expediente <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="open" {{ old('status', $expedient->status) === 'open' ? 'selected' : '' }}>Abierto</option>
                    <option value="in_progress" {{ old('status', $expedient->status) === 'in_progress' ? 'selected' : '' }}>En Trámite</option>
                    <option value="closed" {{ old('status', $expedient->status) === 'closed' ? 'selected' : '' }}>Cerrado</option>
                    <option value="archived" {{ old('status', $expedient->status) === 'archived' ? 'selected' : '' }}>Archivado</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="opened_at" class="form-label fw-semibold small">Fecha de Apertura <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="opened_at" name="opened_at" value="{{ old('opened_at', $expedient->opened_at->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label for="closed_at" class="form-label fw-semibold small">Fecha de Cierre</label>
                <input type="date" class="form-control" id="closed_at" name="closed_at" value="{{ old('closed_at', $expedient->closed_at?->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i> Actualizar Expediente
            </button>
            <a href="{{ route('expedients.show', $expedient) }}" class="btn btn-light border">Cancelar</a>
        </div>
    </form>
</div>
@endsection
