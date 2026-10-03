<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Impresión de Reporte - SGC Universitario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            color: #0f172a;
            font-size: 0.85rem;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .table th {
                background-color: #1e3a8a !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="p-4">
    <div class="no-print d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold mb-0">Vista Previa de Impresión de Reporte</h5>
            <small class="text-muted">Utilice las funciones de su navegador o el botón siguiente para imprimir el reporte.</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="bi bi-printer me-1"></i> Imprimir Reporte
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm">
                <i class="bi bi-x-circle me-1"></i> Cerrar
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h3 class="fw-bold text-primary mb-0">UNIVERSIDAD NACIONAL</h3>
            <div class="fs-6 fw-semibold text-secondary">Sistema de Gestión de Correspondencia Institucional</div>
            <small class="text-muted">Reporte Consolidado de Correspondencia Universitaria</small>
        </div>
        <div class="text-end small">
            <div><strong>Fecha Emisión:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</div>
            <div><strong>Generado por:</strong> {{ $generatedBy->name }}</div>
            <div class="text-muted font-monospace">SEG-{{ strtoupper(substr(md5($generatedAt->timestamp), 0, 8)) }}</div>
        </div>
    </div>

    <div class="row g-2 mb-4">
        <div class="col-3">
            <div class="p-2 border rounded bg-light">
                <small class="text-muted d-block">Total Documentos</small>
                <span class="fs-5 fw-bold">{{ $documents->count() }}</span>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light">
                <small class="text-muted d-block">Recibidos</small>
                <span class="fs-5 fw-bold text-primary">{{ $documents->where('direction.value', 'received')->count() }}</span>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light">
                <small class="text-muted d-block">Enviados</small>
                <span class="fs-5 fw-bold text-success">{{ $documents->where('direction.value', 'sent')->count() }}</span>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light">
                <small class="text-muted d-block">En Trámite</small>
                <span class="fs-5 fw-bold text-warning">{{ $documents->whereNotIn('status.value', ['attended', 'closed', 'archived', 'cancelled'])->count() }}</span>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-sm align-middle small mb-5">
        <thead class="table-primary">
            <tr>
                <th>Folio</th>
                <th>Sentido</th>
                <th>Tipo</th>
                <th>Remitente / Destino</th>
                <th>Asunto</th>
                <th>Dependencia</th>
                <th>Estado</th>
                <th>Término</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
                <tr>
                    <td class="font-monospace fw-bold">{{ $doc->folio }}</td>
                    <td>{{ $doc->direction->label() }}</td>
                    <td>{{ $doc->documentType?->name }}</td>
                    <td>
                        {{ $doc->direction === App\Enums\DocumentDirection::RECEIVED ? $doc->sender_name : $doc->recipient_name }}
                    </td>
                    <td>{{ $doc->subject }}</td>
                    <td class="text-center font-monospace">{{ $doc->department?->code }}</td>
                    <td>{{ $doc->status->label() }}</td>
                    <td class="text-center font-monospace">{{ $doc->due_date?->format('d/m/Y') ?? 'Sin término' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No se encontraron registros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
