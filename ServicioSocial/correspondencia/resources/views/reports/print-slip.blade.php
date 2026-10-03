<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Radicación - {{ $document->folio }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            color: #0f172a;
            font-size: 0.9rem;
        }
        .slip-container {
            max-width: 800px;
            margin: 0 auto;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .slip-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body class="p-4">
    <div class="slip-container">
        <div class="no-print d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h5 class="fw-bold mb-0">Vista de Impresión - Boleta de Radicación</h5>
                <small class="text-muted">Imprima el acuse oficial para entrega al interesado o archivo interno.</small>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer me-1"></i> Imprimir Boleta
                </button>
                <a href="{{ route('documents.slip', $document) }}" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
                </a>
                <button onclick="window.close()" class="btn btn-secondary btn-sm">
                    <i class="bi bi-x-circle me-1"></i> Cerrar
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-start border-bottom border-primary border-2 pb-3 mb-3">
            <div>
                <h3 class="fw-bold text-primary mb-0">UNIVERSIDAD NACIONAL</h3>
                <div class="fw-semibold text-secondary">Sistema de Gestión de Correspondencia Institucional</div>
                <small class="text-muted">Oficialía de Partes &bull; Control y Archivo de Trámites</small>
            </div>
            <div class="border border-2 border-primary rounded p-2 text-center bg-light" style="min-width: 220px;">
                <small class="text-uppercase fw-bold text-muted d-block" style="font-size: 0.72rem;">Folio Oficial</small>
                <span class="fs-4 fw-bold text-primary font-monospace">{{ $document->folio }}</span>
                <div class="font-monospace text-muted" style="letter-spacing: 2px; font-size: 0.75rem;">|| | ||| || |||| | |||</div>
            </div>
        </div>

        <div class="bg-light border rounded text-center py-2 fw-bold text-uppercase mb-3 fs-6">
            Boleta Oficial de Radicación y Acuse de Correspondencia
        </div>

        <table class="table table-bordered table-sm small align-middle mb-4">
            <tbody>
                <tr>
                    <th class="bg-light" style="width: 20%;">Sentido:</th>
                    <td style="width: 30%;" class="fw-bold">{{ $document->direction->label() }}</td>
                    <th class="bg-light" style="width: 20%;">Tipo Documento:</th>
                    <td style="width: 30%;">{{ $document->documentType?->name }}</td>
                </tr>
                <tr>
                    <th class="bg-light">No. Externo / Oficio:</th>
                    <td class="font-monospace">{{ $document->external_number ?? 'S/N' }}</td>
                    <th class="bg-light">Dependencia:</th>
                    <td><strong>{{ $document->department?->name }}</strong> ({{ $document->department?->code }})</td>
                </tr>
                <tr>
                    <th class="bg-light">Remitente:</th>
                    <td>
                        <div class="fw-bold">{{ $document->sender_name }}</div>
                        <small class="text-muted">{{ $document->sender_institution ?? 'Particular' }}</small>
                    </td>
                    <th class="bg-light">Destinatario:</th>
                    <td>
                        <div class="fw-bold">{{ $document->recipient_name }}</div>
                        <small class="text-muted">{{ $document->recipient_institution ?? 'Universidad Nacional' }}</small>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Fecha Documento:</th>
                    <td>{{ $document->document_date?->format('d/m/Y') }}</td>
                    <th class="bg-light">Fecha Radicación:</th>
                    <td class="fw-bold">{{ $document->received_or_sent_at?->format('d/m/Y H:i:s') }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Prioridad:</th>
                    <td><span class="badge {{ $document->priority->badgeClass() }}">{{ $document->priority->label() }}</span></td>
                    <th class="bg-light">Fecha Límite:</th>
                    <td>
                        @if($document->due_date)
                            <strong class="text-danger font-monospace">{{ $document->due_date->format('d/m/Y') }}</strong>
                        @else
                            <span class="text-muted">Sin término fijado</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Estado de Trámite:</th>
                    <td><span class="badge {{ $document->status->badgeClass() }}">{{ $document->status->label() }}</span></td>
                    <th class="bg-light">Funcionario Asignado:</th>
                    <td>{{ $document->assignedUser?->name ?? 'Sin asignar' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Asunto Oficial:</th>
                    <td colspan="3" class="fw-medium">{{ $document->subject }}</td>
                </tr>
                @if($document->expedients->isNotEmpty())
                <tr>
                    <th class="bg-light">Expediente Digital:</th>
                    <td colspan="3" class="font-monospace">
                        @foreach($document->expedients as $exp)
                            <span class="badge bg-dark-subtle text-dark border me-1">[{{ $exp->code }}] {{ $exp->title }}</span>
                        @endforeach
                    </td>
                </tr>
                @endif
                @if($document->attachments->isNotEmpty())
                <tr>
                    <th class="bg-light">Adjuntos Verificados:</th>
                    <td colspan="3" class="font-monospace small">
                        @foreach($document->attachments as $att)
                            <span class="badge bg-light text-dark border me-1">
                                <i class="bi bi-paperclip me-1"></i>{{ $att->original_name }} ({{ $att->formatted_size }})
                            </span>
                        @endforeach
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="row g-3 mb-5">
            <div class="col-6">
                <div class="border border-secondary border-dashed p-3 text-center rounded bg-light" style="min-height: 90px;">
                    <strong class="small text-muted text-uppercase d-block mb-2">Sello Oficial de Recepción</strong>
                    <div class="small fw-semibold text-secondary">OFICIALÍA DE PARTES &bull; CORRESPONDENCIA</div>
                    <div class="small text-muted">DOCUMENTO RADICADO Y REGISTRADO</div>
                </div>
            </div>
            <div class="col-6">
                <div class="border border-secondary border-dashed p-3 text-center rounded bg-light" style="min-height: 90px;">
                    <strong class="small text-muted text-uppercase d-block mb-2">Acuse de Recibido Interesado</strong>
                    <div class="small text-muted">FECHA: _____ / _____ / __________ HORA: ____:____</div>
                    <div class="small text-muted mt-2">NOMBRE Y FIRMA DE QUIEN RECIBE</div>
                </div>
            </div>
        </div>

        <div class="border-top pt-2 text-center text-muted small" style="font-size: 0.72rem;">
            Documento de control oficial emitido por el Sistema de Gestión de Correspondencia Universitaria &bull; Folio: {{ $document->folio }} &bull; Código Criptográfico de Control: SHA-{{ strtoupper(substr(hash('sha256', $document->folio . $document->created_at), 0, 12)) }}
        </div>
    </div>
</body>
</html>
