<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acuse Oficial - {{ $document->folio }}</title>
    <style>
        @page {
            margin: 1.2cm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            color: #0f172a;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .institution {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .system-title {
            font-size: 9.5pt;
            color: #475569;
            font-weight: bold;
        }
        .slip-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            text-align: center;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        .folio-box {
            border: 2px solid #1e3a8a;
            background-color: #f8fafc;
            padding: 8px;
            text-align: center;
        }
        .folio-number {
            font-family: monospace;
            font-size: 15pt;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 1px;
        }
        .barcode {
            font-family: monospace;
            font-size: 11pt;
            letter-spacing: 3px;
            margin-top: 4px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9pt;
            vertical-align: top;
        }
        .data-table td.label {
            background-color: #f8fafc;
            font-weight: bold;
            color: #475569;
            width: 22%;
        }
        .stamp-box {
            width: 100%;
            border: 1px dashed #64748b;
            padding: 8px;
            min-height: 70px;
            text-align: center;
            font-size: 8pt;
            color: #64748b;
            background-color: #fafafa;
        }
        .signatures {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-cell {
            width: 30%;
            text-align: center;
            border-top: 1px solid #000000;
            padding-top: 4px;
            font-size: 8pt;
        }
        .security-footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 7.5pt;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="institution">Universidad Nacional</div>
                <div class="system-title">Sistema de Gestión de Correspondencia Institucional</div>
                <small style="color: #64748b;">Oficialía de Partes &bull; Control y Archivo de Trámites</small>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="folio-box">
                    <div style="font-size: 8pt; text-transform: uppercase; font-weight: bold; color: #64748b;">Folio Oficial</div>
                    <div class="folio-number">{{ $document->folio }}</div>
                    <div class="barcode">|| | ||| || |||| | |||</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="slip-title">
        Boleta Oficial de Radicación y Acuse de Correspondencia
    </div>

    <table class="data-table">
        <tr>
            <td class="label">Sentido de Trámite:</td>
            <td style="width: 28%; font-weight: bold;">{{ $document->direction->label() }}</td>
            <td class="label">Tipo de Documento:</td>
            <td style="width: 28%;">{{ $document->documentType?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Número Externo / Oficio:</td>
            <td style="font-family: monospace;">{{ $document->external_number ?? 'S/N' }}</td>
            <td class="label">Dependencia Responsable:</td>
            <td><strong>{{ $document->department?->name }}</strong> ({{ $document->department?->code }})</td>
        </tr>
        <tr>
            <td class="label">Remitente:</td>
            <td>
                <strong>{{ $document->sender_name }}</strong><br>
                <small>{{ $document->sender_institution ?? 'Particular / Sin institución' }}</small>
            </td>
            <td class="label">Destinatario:</td>
            <td>
                <strong>{{ $document->recipient_name }}</strong><br>
                <small>{{ $document->recipient_institution ?? 'Universidad Nacional' }}</small>
            </td>
        </tr>
        <tr>
            <td class="label">Fecha del Documento:</td>
            <td>{{ $document->document_date?->format('d/m/Y') ?? 'N/A' }}</td>
            <td class="label">Fecha y Hora de Radicación:</td>
            <td><strong>{{ $document->received_or_sent_at?->format('d/m/Y H:i:s') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Prioridad:</td>
            <td><strong>{{ $document->priority->label() }}</strong></td>
            <td class="label">Fecha Límite (Término):</td>
            <td>
                @if($document->due_date)
                    <strong style="color: #b91c1c;">{{ $document->due_date->format('d/m/Y') }}</strong>
                @else
                    <span>Sin término legal fijado</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Estado Actual:</td>
            <td>{{ $document->status->label() }}</td>
            <td class="label">Funcionario Asignado:</td>
            <td>{{ $document->assignedUser?->name ?? 'Pendiente de turno' }}</td>
        </tr>
        <tr>
            <td class="label">Asunto Oficial:</td>
            <td colspan="3" style="font-size: 9.5pt;">
                {{ $document->subject }}
            </td>
        </tr>
        @if($document->expedients->isNotEmpty())
        <tr>
            <td class="label">Expediente(s) Digital(es):</td>
            <td colspan="3">
                @foreach($document->expedients as $exp)
                    <span style="font-family: monospace; font-weight: bold;">[{{ $exp->code }}] {{ $exp->title }}</span><br>
                @endforeach
            </td>
        </tr>
        @endif
        @if($document->attachments->isNotEmpty())
        <tr>
            <td class="label">Adjuntos Digitalizados:</td>
            <td colspan="3" style="font-size: 8pt;">
                @foreach($document->attachments as $att)
                    &bull; {{ $att->original_name }} ({{ $att->formatted_size }}) - Hash SHA-256: {{ substr($att->hash, 0, 16) }}...<br>
                @endforeach
            </td>
        </tr>
        @endif
    </table>

    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="stamp-box">
                    <strong>SELLO INSTITUCIONAL DE RADICACIÓN</strong><br><br>
                    <span>VENTANILLA DE CORRESPONDENCIA</span><br>
                    <span>RECIBIDO Y RADICADO CONFORME A REGLAMENTO</span>
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <div class="stamp-box">
                    <strong>ACUSE DE RECIBIDO POR EL INTERESADO</strong><br><br>
                    <span>FECHA: ____ / ____ / ________  HORA: ______:______</span><br>
                    <span>NOMBRE Y FIRMA DE QUIEN RECIBE</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="security-footer">
        Este documento es un comprobante oficial de correspondencia universitaria. La autenticidad e integridad del registro puede verificarse con el folio oficial [{{ $document->folio }}] en el sistema institucional &bull; Código Criptográfico de Control: SHA-{{ strtoupper(substr(hash('sha256', $document->folio . $document->created_at), 0, 12)) }}
    </div>
</body>
</html>
