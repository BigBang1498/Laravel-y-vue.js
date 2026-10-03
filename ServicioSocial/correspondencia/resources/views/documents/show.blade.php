@extends('layouts.app')

@section('title', 'Expediente: ' . $document->folio)

@section('content')
<div class="mb-4">
    <div class="d-flex flex-wrap gap-2 mb-2">
        @if($document->direction === App\Enums\DocumentDirection::RECEIVED)
            <a href="{{ route('received-documents.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver a Recibidos
            </a>
        @else
            <a href="{{ route('sent-documents.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver a Enviados
            </a>
        @endif
        <a href="{{ route('tracking.pending') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-clock-history me-1"></i> Pendientes
        </a>
    </div>

    <div class="card card-custom p-4 border-start border-4 {{ $document->direction === App\Enums\DocumentDirection::RECEIVED ? 'border-primary' : 'border-success' }}">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h3 class="fw-bold font-monospace text-dark mb-0">{{ $document->folio }}</h3>
                    <span class="badge {{ $document->direction->badgeClass() }} px-3 py-1">
                        <i class="bi {{ $document->direction->icon() }} me-1"></i> {{ $document->direction->label() }}
                    </span>
                    <span class="badge {{ $document->priority->badgeClass() }} px-3 py-1">
                        <i class="bi {{ $document->priority->icon() }} me-1"></i> Prioridad {{ $document->priority->label() }}
                    </span>
                    <span class="badge {{ $document->status->badgeClass() }} px-3 py-1">
                        <i class="bi {{ $document->status->icon() }} me-1"></i> Estado: {{ $document->status->label() }}
                    </span>
                </div>
                <div class="text-secondary small">
                    {{ $document->external_number ? 'Oficio / Referencia Externa: ' . $document->external_number : 'Sin número de oficio externo' }}
                    &bull; Radicado por: <strong>{{ $document->creator->name }}</strong> el {{ $document->received_or_sent_at->format('d/m/Y H:i') }} hrs.
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                @can('upload', [App\Models\Attachment::class, $document])
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadAttachmentModal">
                        <i class="bi bi-paperclip me-1"></i> Adjuntar
                    </button>
                @endcan

                @if($document->direction === App\Enums\DocumentDirection::RECEIVED)
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#respondModal">
                        <i class="bi bi-reply-fill me-1"></i> Responder
                    </button>
                @endif

                @can('assign', $document)
                    <button type="button" class="btn btn-warning text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="bi bi-person-check-fill me-1"></i> {{ $document->currentAssignment ? 'Reasignar' : 'Asignar' }}
                    </button>
                @endcan

                @can('changeStatus', $document)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changeStatusModal">
                        <i class="bi bi-arrow-repeat me-1"></i> Estado
                    </button>
                @endcan

                @can('update', $document)
                    @if($document->direction === App\Enums\DocumentDirection::RECEIVED)
                        <a href="{{ route('received-documents.edit', $document) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                    @else
                        <a href="{{ route('sent-documents.edit', $document) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                    @endif
                @endcan

                <div class="btn-group">
                    <a href="{{ route('documents.slip', $document) }}" class="btn btn-outline-danger" title="Descargar Boleta de Radicación en PDF">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Boleta PDF
                    </a>
                    <a href="{{ route('documents.print-slip', $document) }}" target="_blank" class="btn btn-outline-dark" title="Imprimir Boleta Oficial">
                        <i class="bi bi-printer"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text-fill text-primary"></i>
                Contenido Institucional
            </h5>
            <div class="mb-3">
                <span class="text-muted small d-block">Asunto Oficial:</span>
                <h5 class="text-dark fw-bold">{{ $document->subject }}</h5>
            </div>

            <div class="mb-4">
                <span class="text-muted small d-block mb-1">Descripción / Extracto:</span>
                <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-wrap;">{{ $document->description ?? 'Sin descripción detallada capturada.' }}</div>
            </div>

            <div class="row g-3 border-top pt-3">
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-light h-100">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-up me-1 text-primary"></i> Remitente</h6>
                        <div class="fw-semibold text-dark">{{ $document->sender_name }}</div>
                        <small class="text-muted d-block">{{ $document->sender_institution ?? 'Particular' }}</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-light h-100">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-down me-1 text-success"></i> Destinatario</h6>
                        <div class="fw-semibold text-dark">{{ $document->recipient_name }}</div>
                        <small class="text-muted d-block">{{ $document->recipient_institution ?? 'Particular' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-paperclip text-primary"></i>
                    Archivos Digitales y Evidencias
                </h5>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-light text-dark border">{{ $document->attachments->count() }} archivo(s)</span>
                    @can('upload', [App\Models\Attachment::class, $document])
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadAttachmentModal">
                            <i class="bi bi-plus-lg me-1"></i> Adjuntar
                        </button>
                    @endcan
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                @forelse($document->attachments as $attachment)
                    <div class="p-3 border rounded-3 bg-light d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi {{ $attachment->icon_class }} fs-2"></i>
                            <div>
                                <div class="fw-bold text-dark">{{ $attachment->original_name }}</div>
                                <div class="text-muted small">
                                    <span>{{ $attachment->formatted_size }}</span> &bull;
                                    <span>Subido por: <strong>{{ $attachment->uploader->name }}</strong></span> &bull;
                                    <span>{{ $attachment->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($attachment->description)
                                    <small class="text-secondary d-block mt-1 fst-italic">{{ $attachment->description }}</small>
                                @endif
                                @if($attachment->file_hash)
                                    <div class="mt-1">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" title="SHA-256: {{ $attachment->file_hash }}">
                                            <i class="bi bi-shield-check me-1"></i> SHA-256: {{ substr($attachment->file_hash, 0, 12) }}...
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('documents.attachments.download', [$document, $attachment]) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-download me-1"></i> Descargar
                            </a>
                            @can('delete', $attachment)
                                <form action="{{ route('documents.attachments.destroy', [$document, $attachment]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Confirma que desea eliminar este archivo adjunto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-file-earmark-x fs-2 d-block mb-1 text-secondary"></i>
                        No hay archivos adjuntos digitalizados para este documento.
                    </div>
                @endforelse
            </div>
        </div>

        @if($document->direction === App\Enums\DocumentDirection::RECEIVED)
            <div class="card card-custom p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-reply-all-fill text-success"></i>
                        Respuestas y Resoluciones Oficiales
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#respondModal">
                        <i class="bi bi-plus-lg me-1"></i> Emitir Respuesta
                    </button>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse($document->responses as $response)
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold text-dark">
                                    <i class="bi bi-person-circle me-1 text-primary"></i> {{ $response->user->name }}
                                    <small class="text-muted">({{ $response->user->position ?? 'Funcionario' }})</small>
                                </div>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $response->response_date->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="text-secondary small mb-2" style="white-space: pre-wrap;">{{ $response->response_text }}</div>
                            @if($response->sentDocument)
                                <div class="p-2 border rounded bg-white small d-flex align-items-center justify-content-between">
                                    <span>
                                        <i class="bi bi-link-45deg text-primary me-1"></i> Oficio de Salida Vinculado:
                                        <strong>{{ $response->sentDocument->folio }}</strong> - {{ $response->sentDocument->subject }}
                                    </span>
                                    <a href="{{ route('documents.show', $response->sentDocument) }}" class="btn btn-sm btn-outline-primary py-0">
                                        Ver Oficio
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-chat-left-dots fs-2 d-block mb-1 text-secondary"></i>
                            No se han emitido respuestas oficiales aún para este trámite.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-folder2-open text-primary"></i>
                    Expedientes Digitales Vinculados
                </h5>
                @if($availableExpedients->isNotEmpty())
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#linkToExpedientModal">
                        <i class="bi bi-plus-lg me-1"></i> Vincular a Expediente
                    </button>
                @endif
            </div>

            <div class="d-flex flex-wrap gap-2">
                @forelse($document->expedients as $exp)
                    <div class="p-3 border rounded-3 bg-light d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-folder-fill text-warning fs-3"></i>
                            <div>
                                <a href="{{ route('expedients.show', $exp) }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $exp->code }} - {{ $exp->title }}
                                </a>
                                <div class="text-muted small">
                                    {{ $exp->department->name }} &bull; Estado: <span class="badge {{ $exp->status_badge_class }} py-0">{{ $exp->status_label }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('expedients.show', $exp) }}" class="btn btn-sm btn-outline-primary">
                            Abrir Expediente
                        </a>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small w-100">
                        Este documento no se encuentra vinculado a ningún expediente temático digital.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-person-lines-fill text-primary"></i>
                    Historial de Turnos y Asignaciones
                </h5>
                <span class="badge bg-light text-dark border">{{ $document->assignments->count() }} registro(s)</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-uppercase">
                        <tr>
                            <th>Fecha</th>
                            <th>Departamento</th>
                            <th>Funcionario Asignado</th>
                            <th>Asignado Por</th>
                            <th>Término</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($document->assignments as $assign)
                            <tr>
                                <td>{{ $assign->assigned_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-semibold text-dark">{{ $assign->department->name }}</td>
                                <td>
                                    @if($assign->assignedUser)
                                        <div class="fw-semibold">{{ $assign->assignedUser->name }}</div>
                                        <small class="text-muted">{{ $assign->assignedUser->position ?? 'Funcionario' }}</small>
                                    @else
                                        <span class="text-muted">Área General</span>
                                    @endif
                                </td>
                                <td>{{ $assign->assigner->name }}</td>
                                <td>{{ $assign->due_date ? $assign->due_date->format('d/m/Y') : 'Sin término' }}</td>
                                <td>
                                    @if($assign->is_current)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i> Vigente
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">Relevada</span>
                                    @endif
                                </td>
                            </tr>
                            @if($assign->instructions)
                                <tr class="table-light">
                                    <td colspan="6" class="py-2 px-3 text-secondary fst-italic">
                                        <i class="bi bi-chat-left-quote me-1 text-primary"></i> <strong>Instrucciones:</strong> {{ $assign->instructions }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No se registran asignaciones formales registradas para este documento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i>
                Trazabilidad y Línea de Tiempo (Bitácora Forense)
            </h5>

            <div class="position-relative ps-4 border-start border-2 border-primary border-opacity-25 ms-2 my-2">
                @forelse($document->history as $history)
                    <div class="position-relative mb-4">
                        <div class="position-absolute translate-middle-x rounded-circle bg-primary" style="left: -25px; top: 4px; width: 12px; height: 12px; border: 2px solid white;"></div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                            <div>
                                <span class="badge bg-dark-subtle text-dark fw-semibold text-uppercase me-2">{{ $history->action }}</span>
                                @if($history->new_status)
                                    <span class="badge bg-info-subtle text-info me-1">
                                        @if($history->old_status)
                                            {{ $history->old_status }} &rarr;
                                        @endif
                                        {{ $history->new_status }}
                                    </span>
                                @endif
                            </div>
                            <small class="text-muted font-monospace">
                                <i class="bi bi-calendar-event me-1"></i>{{ $history->created_at->format('d/m/Y H:i:s') }}
                            </small>
                        </div>
                        <div class="p-3 bg-light rounded-3 text-secondary small">
                            <p class="mb-1 text-dark">{{ $history->description }}</p>
                            <div class="text-muted border-top pt-1 mt-1" style="font-size: 0.75rem;">
                                <span><i class="bi bi-person me-1"></i> Operador: <strong>{{ $history->user?->name ?? 'Sistema Automatizado' }}</strong></span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small">Sin eventos registrados en la bitácora de trazabilidad.</p>
                @endforelse
            </div>
        </div>

        @if($document->observations)
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-left-dots-fill text-secondary"></i>
                    Bitácora de Observaciones y Resoluciones
                </h5>
                <div class="p-3 bg-light border rounded-3 font-monospace small text-secondary" style="white-space: pre-wrap;">{{ $document->observations }}</div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person-check-fill text-primary"></i>
                Responsable y Asignación Actual
            </h5>

            @if($document->currentAssignment)
                <div class="p-3 border rounded-3 bg-light mb-3">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Departamento:</div>
                    <div class="fw-bold text-dark fs-6">{{ $document->currentAssignment->department->name }}</div>

                    <div class="text-muted small text-uppercase fw-semibold mt-3 mb-1">Funcionario Designado:</div>
                    <div class="fw-semibold text-primary">
                        {{ $document->currentAssignment->assignedUser?->name ?? 'Área General (Sin funcionario individual)' }}
                    </div>
                    @if($document->currentAssignment->assignedUser)
                        <small class="text-muted d-block">{{ $document->currentAssignment->assignedUser->position ?? 'Puesto institucional' }}</small>
                        <small class="text-muted d-block">{{ $document->currentAssignment->assignedUser->email }}</small>
                    @endif

                    <div class="border-top pt-2 mt-2 text-muted small">
                        <div>Turnado por: <strong>{{ $document->currentAssignment->assigner->name }}</strong></div>
                        <div>Fecha: {{ $document->currentAssignment->assigned_at->format('d/m/Y H:i') }}</div>
                    </div>

                    @if($document->currentAssignment->instructions)
                        <div class="border-top pt-2 mt-2 text-secondary small">
                            <span class="fw-semibold text-dark d-block">Instrucciones:</span>
                            {{ $document->currentAssignment->instructions }}
                        </div>
                    @endif
                </div>
            @else
                <div class="p-3 border rounded-3 bg-light mb-3 text-center text-muted small">
                    <i class="bi bi-person-x fs-2 d-block mb-1 text-secondary"></i>
                    Este documento aún no tiene un funcionario responsable asignado individualmente.
                </div>
            @endif
        </div>

        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-calendar3 text-primary"></i>
                Control de Términos y Fechas
            </h5>

            @if($document->due_date)
                <div class="mb-3 p-3 rounded-3 text-center border
                    @if($document->traffic_light === 'green') bg-success-subtle border-success-subtle text-success
                    @elseif($document->traffic_light === 'yellow') bg-warning-subtle border-warning-subtle text-warning-emphasis
                    @elseif($document->traffic_light === 'red') bg-danger-subtle border-danger-subtle text-danger
                    @else bg-light text-muted @endif">
                    <div class="fw-bold fs-5">
                        @if($document->days_remaining > 0)
                            <i class="bi bi-hourglass-top me-1"></i> {{ $document->days_remaining }} días restantes
                        @elseif($document->days_remaining === 0)
                            <i class="bi bi-exclamation-circle-fill me-1"></i> Vence el día de hoy
                        @elseif($document->days_remaining < 0)
                            <i class="bi bi-alarm-fill me-1"></i> Vencido hace {{ abs($document->days_remaining) }} días
                        @else
                            <i class="bi bi-check-circle-fill me-1"></i> Trámite Concluido
                        @endif
                    </div>
                    <small class="d-block mt-1">Fecha Límite: <strong>{{ $document->due_date->format('d/m/Y') }}</strong></small>
                </div>
            @endif

            <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Fecha del Documento:</span>
                    <strong>{{ $document->document_date->format('d/m/Y') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Fecha de Radicación:</span>
                    <strong>{{ $document->received_or_sent_at->format('d/m/Y H:i') }}</strong>
                </li>
                @if($document->attended_at)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 text-success">
                        <span>Fecha de Atención:</span>
                        <strong>{{ $document->attended_at->format('d/m/Y H:i') }}</strong>
                    </li>
                @endif
                @if($document->closed_at)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 text-secondary">
                        <span>Fecha de Cierre:</span>
                        <strong>{{ $document->closed_at->format('d/m/Y H:i') }}</strong>
                    </li>
                @endif
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Tipo Documental:</span>
                    <strong class="text-primary">{{ $document->documentType->name }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Departamento Radicador:</span>
                    <strong class="text-dark">{{ $document->department->name }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Nivel de Confidencialidad:</span>
                    <span class="badge bg-light text-dark border text-uppercase">{{ $document->security_level }}</span>
                </li>
            </ul>
        </div>

        <div class="card card-custom p-4 bg-light border-0">
            <h6 class="fw-bold mb-3"><i class="bi bi-link-45deg me-1 text-primary"></i> Módulos Vinculados</h6>
            <div class="d-flex flex-column gap-2 small">
                <div class="p-2 border rounded bg-white text-success fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i> Asignaciones y Turnos (Activo)
                </div>
                <div class="p-2 border rounded bg-white text-success fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i> Archivos Digitales Seguros (Activo)
                </div>
                <div class="p-2 border rounded bg-white text-success fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i> Respuestas Oficiales (Activo)
                </div>
                <div class="p-2 border rounded bg-white text-success fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i> Expedientes Digitales (Activo)
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadAttachmentModal" tabindex="-1" aria-labelledby="uploadAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('documents.attachments.store', $document) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="uploadAttachmentModalLabel">
                        <i class="bi bi-paperclip me-1 text-primary"></i> Adjuntar Archivo Digital
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="attachment_file" class="form-label fw-semibold small">Seleccionar Archivo <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="attachment_file" name="file" required>
                        <div class="form-text small">Admite PDF, Word, Excel, PowerPoint, ZIP, RAR e imágenes. Máximo 20 MB.</div>
                    </div>

                    <div class="mb-3">
                        <label for="attachment_description" class="form-label fw-semibold small">Descripción o Etiqueta del Archivo</label>
                        <input type="text" class="form-control" id="attachment_description" name="description" placeholder="Ej: Acuse de recepción escaneado con sello oficial">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Subir y Proteger
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($document->direction === App\Enums\DocumentDirection::RECEIVED)
    <div class="modal fade" id="respondModal" tabindex="-1" aria-labelledby="respondModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('documents.responses.store', $document) }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="respondModalLabel">
                            <i class="bi bi-reply-fill me-1 text-success"></i> Emitir Respuesta Oficial
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="response_text" class="form-label fw-semibold small">Texto o Resumen de la Respuesta / Resolución <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="response_text" name="response_text" rows="4" placeholder="Redacte la contestación institucional, términos de atención o acuerdo dictado..." required></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="response_date" class="form-label fw-semibold small">Fecha de Emisión <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="response_date" name="response_date" value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="sent_document_id" class="form-label fw-semibold small">Vincular a Oficio de Salida (Opcional)</label>
                                <select name="sent_document_id" id="sent_document_id" class="form-select">
                                    <option value="">-- Sin vincular oficio de salida --</option>
                                    @foreach($availableSentDocuments as $sentDoc)
                                        <option value="{{ $sentDoc->id }}">
                                            [{{ $sentDoc->folio }}] - {{ Str::limit($sentDoc->subject, 40) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-check p-3 bg-light rounded-3 border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="mark_as_attended" value="1" id="mark_as_attended" checked>
                            <label class="form-check-label fw-semibold text-dark small" for="mark_as_attended">
                                Actualizar estado del documento recibido a "Atendido" inmediatamente
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check2-circle me-1"></i> Registrar Respuesta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@if($availableExpedients->isNotEmpty())
    <div class="modal fade" id="linkToExpedientModal" tabindex="-1" aria-labelledby="linkToExpedientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="linkToExpedientForm" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="linkToExpedientModalLabel">
                            <i class="bi bi-folder-symlink-fill me-1 text-primary"></i> Vincular a Expediente Digital
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="select_expedient" class="form-label fw-semibold small">Seleccione Expediente Digital <span class="text-danger">*</span></label>
                            <select id="select_expedient" class="form-select" required onchange="document.getElementById('linkToExpedientForm').action = '/expedients/' + this.value + '/add-document';">
                                <option value="">-- Seleccionar Expediente --</option>
                                @foreach($availableExpedients as $exp)
                                    <option value="{{ $exp->id }}">{{ $exp->code }} - {{ $exp->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="document_id" value="{{ $document->id }}">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-link-45deg me-1"></i> Vincular
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('documents.assignments.store', $document) }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="assignModalLabel">
                        <i class="bi bi-person-check-fill me-1 text-warning"></i>
                        {{ $document->currentAssignment ? 'Reasignar / Turnar Documento' : 'Asignar Documento' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to_department_id" class="form-label fw-semibold small">
                            Departamento Responsable <span class="text-danger">*</span>
                        </label>
                        <select name="assigned_to_department_id" id="assigned_to_department_id" class="form-select" required>
                            <option value="">-- Seleccione Departamento --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('assigned_to_department_id', $document->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="assigned_to_user_id" class="form-label fw-semibold small">
                            Funcionario / Responsable Individual (Opcional)
                        </label>
                        <select name="assigned_to_user_id" id="assigned_to_user_id" class="form-select">
                            <option value="">-- Asignar al Área General / Sin asignar a funcionario específico --</option>
                            @foreach($assignableUsers as $u)
                                <option value="{{ $u->id }}" data-department-id="{{ $u->department_id }}" {{ old('assigned_to_user_id', $document->current_assigned_user_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->position ?? 'Funcionario' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label fw-semibold small">Fecha Límite de Atención / Término</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" min="{{ date('Y-m-d') }}" value="{{ old('due_date', $document->due_date?->format('Y-m-d')) }}">
                    </div>

                    <div class="mb-3">
                        <label for="instructions" class="form-label fw-semibold small">Instrucciones o Indicaciones de Turno</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="3" placeholder="Instrucciones para la atención del asunto...">{{ old('instructions') }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Guardar Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('documents.change-status', $document) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="changeStatusModalLabel">
                        <i class="bi bi-arrow-repeat me-1 text-primary"></i> Cambiar Estado del Documento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        Estado Actual: <strong>{{ $document->status->label() }}</strong>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold small">Nuevo Estado <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="">-- Seleccione estado permitido --</option>
                            @php
                                $allowed = auth()->user()->isAdmin()
                                    ? App\Enums\DocumentStatus::cases()
                                    : $document->status->allowedTransitions();
                            @endphp
                            @foreach($allowed as $statusOption)
                                @if($statusOption !== $document->status)
                                    <option value="{{ $statusOption->value }}">
                                        {{ $statusOption->label() }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="modal_observations" class="form-label fw-semibold small">Motivo / Justificación del Cambio</label>
                        <textarea class="form-control" id="modal_observations" name="observations" rows="3" placeholder="Indique la causa o resolución del cambio de estado..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Nuevo Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deptSelect = document.getElementById('assigned_to_department_id');
        const userSelect = document.getElementById('assigned_to_user_id');

        if (deptSelect && userSelect) {
            deptSelect.addEventListener('change', function () {
                const deptId = this.value;
                if (!deptId) return;

                fetch(`/departments/${deptId}/users`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(users => {
                    userSelect.innerHTML = '<option value="">-- Asignar al Área General / Sin asignar a funcionario específico --</option>';
                    users.forEach(u => {
                        const opt = document.createElement('option');
                        opt.value = u.id;
                        opt.textContent = `${u.name} (${u.position || 'Funcionario'})`;
                        userSelect.appendChild(opt);
                    });
                })
                .catch(err => console.error(err));
            });
        }
    });
</script>
@endsection
