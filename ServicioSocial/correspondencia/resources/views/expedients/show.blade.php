@extends('layouts.app')

@section('title', 'Expediente: ' . $expedient->code)

@section('content')
<div class="mb-4">
    <div class="d-flex flex-wrap gap-2 mb-2">
        <a href="{{ route('expedients.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver a Expedientes
        </a>
    </div>

    <div class="card card-custom p-4 border-start border-4 border-primary">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h3 class="fw-bold font-monospace text-dark mb-0">{{ $expedient->code }}</h3>
                    <span class="badge {{ $expedient->status_badge_class }} px-3 py-1">
                        {{ $expedient->status_label }}
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-1">
                        <i class="bi bi-building me-1"></i> {{ $expedient->department->name }}
                    </span>
                </div>
                <h4 class="text-dark fw-bold mb-1">{{ $expedient->title }}</h4>
                <div class="text-secondary small">
                    Responsable: <strong>{{ $expedient->responsibleUser?->name ?? 'Área General' }}</strong> &bull;
                    Apertura: {{ $expedient->opened_at->format('d/m/Y') }}
                    @if($expedient->closed_at)
                        &bull; Cierre: <strong>{{ $expedient->closed_at->format('d/m/Y') }}</strong>
                    @endif
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                @can('manageDocuments', $expedient)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#linkDocumentModal">
                        <i class="bi bi-link-45deg me-1"></i> Vincular Documento
                    </button>
                @endcan

                @can('update', $expedient)
                    <a href="{{ route('expedients.edit', $expedient) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                @endcan
            </div>
        </div>

        @if($expedient->description)
            <div class="mt-3 pt-3 border-top text-secondary small">
                <span class="fw-semibold text-dark">Descripción institucional:</span> {{ $expedient->description }}
            </div>
        @endif
    </div>
</div>

<div class="card card-custom overflow-hidden mb-4">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-text-fill text-primary"></i>
            Documentos Integrados al Expediente
        </h5>
        <span class="badge bg-light text-dark border">{{ $expedient->documents->count() }} documento(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary small text-uppercase">
                <tr>
                    <th class="ps-4">Folio</th>
                    <th>Asunto</th>
                    <th>Tipo</th>
                    <th>Flujo</th>
                    <th>Fecha Registro</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expedient->documents as $doc)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-monospace text-primary">{{ $doc->folio }}</span>
                            @if($doc->external_number)
                                <div class="text-muted small">{{ $doc->external_number }}</div>
                            @endif
                        </td>
                        <td style="max-width: 320px;">
                            <div class="fw-semibold text-dark text-truncate" title="{{ $doc->subject }}">{{ $doc->subject }}</div>
                            <small class="text-muted text-truncate d-block">
                                {{ $doc->direction === App\Enums\DocumentDirection::RECEIVED ? 'Remitente: ' . $doc->sender_name : 'Destinatario: ' . $doc->recipient_name }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $doc->documentType->code }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $doc->direction->badgeClass() }}">
                                <i class="bi {{ $doc->direction->icon() }} me-1"></i> {{ $doc->direction->label() }}
                            </span>
                        </td>
                        <td>{{ $doc->received_or_sent_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $doc->status->badgeClass() }}">
                                {{ $doc->status->label() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $doc->priority->badgeClass() }}">
                                {{ $doc->priority->label() }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-outline-primary" title="Ver Documento">
                                    <i class="bi bi-folder2-open"></i>
                                </a>
                                @can('manageDocuments', $expedient)
                                    <form action="{{ route('expedients.remove-document', [$expedient, $doc]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Confirma que desea desvincular este documento del expediente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Desvincular">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-plus fs-1 d-block mb-2 text-secondary"></i>
                            Este expediente aún no contiene documentos vinculados. Utilice el botón "Vincular Documento" para agregar correspondencia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="linkDocumentModal" tabindex="-1" aria-labelledby="linkDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('expedients.add-document', $expedient) }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="linkDocumentModalLabel">
                        <i class="bi bi-link-45deg me-1 text-primary"></i> Vincular Documento a {{ $expedient->code }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_id" class="form-label fw-semibold small">Seleccione la correspondencia institucional a incorporar <span class="text-danger">*</span></label>
                        <select name="document_id" id="document_id" class="form-select" required>
                            <option value="">-- Seleccione un documento disponible --</option>
                            @foreach($availableDocuments as $availDoc)
                                <option value="{{ $availDoc->id }}">
                                    [{{ $availDoc->folio }}] - {{ Str::limit($availDoc->subject, 65) }} ({{ $availDoc->documentType->name }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Se muestran documentos bajo su ámbito de consulta que aún no forman parte de este expediente.</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Vincular al Expediente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
