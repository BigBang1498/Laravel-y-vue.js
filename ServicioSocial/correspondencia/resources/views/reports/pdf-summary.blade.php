<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Institucional de Correspondencia</title>
    <style>
        @page {
            margin: 1.5cm 1cm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.25;
        }
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header table {
            width: 100%;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 9pt;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 8pt;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }
        .data-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 6px 4px;
            border: 1px solid #1e3a8a;
        }
        .data-table td {
            padding: 5px 4px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 3px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #cbd5e1;
            font-size: 7pt;
            color: #64748b;
            text-align: center;
            line-height: 25px;
        }
        .signatures {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 30%;
            text-align: center;
            border-top: 1px solid #000000;
            padding-top: 5px;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 70%;">
                    <div class="title">SGC Universitario</div>
                    <div class="subtitle">Sistema de Gestión de Correspondencia Institucional &bull; Reporte Ejecutivo</div>
                </td>
                <td style="width: 30%; text-align: right; font-size: 8pt; color: #475569;">
                    <div><strong>Fecha Emisión:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</div>
                    <div><strong>Emitido por:</strong> {{ $generatedBy->name }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Total Registros:</strong> {{ $documents->count() }} correspondencias</td>
            <td><strong>Recibidos:</strong> {{ $documents->where('direction.value', 'received')->count() }}</td>
            <td><strong>Enviados:</strong> {{ $documents->where('direction.value', 'sent')->count() }}</td>
            <td><strong>En Trámite:</strong> {{ $documents->whereNotIn('status.value', ['attended', 'closed', 'archived', 'cancelled'])->count() }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 13%;">Folio</th>
                <th style="width: 9%;">Sentido</th>
                <th style="width: 13%;">Tipo Documento</th>
                <th style="width: 18%;">Remitente / Destino</th>
                <th style="width: 25%;">Asunto</th>
                <th style="width: 8%;">Depto</th>
                <th style="width: 8%;">Estado</th>
                <th style="width: 6%;">Término</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
                <tr>
                    <td style="font-family: monospace; font-weight: bold;">{{ $doc->folio }}</td>
                    <td>{{ $doc->direction->label() }}</td>
                    <td>{{ $doc->documentType?->name }}</td>
                    <td>
                        {{ $doc->direction === App\Enums\DocumentDirection::RECEIVED ? $doc->sender_name : $doc->recipient_name }}
                    </td>
                    <td>{{ Str::limit($doc->subject, 65) }}</td>
                    <td style="text-align: center;">{{ $doc->department?->code }}</td>
                    <td>{{ $doc->status->label() }}</td>
                    <td style="text-align: center;">
                        {{ $doc->due_date?->format('d/m/Y') ?? 'S/T' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 15px;">No se registraron documentos bajo los criterios especificados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión de Correspondencia Universitaria &bull; Folio de Seguridad: SEC-{{ strtoupper(substr(md5($generatedAt->timestamp . $generatedBy->id), 0, 10)) }}
    </div>
</body>
</html>
