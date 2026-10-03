<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error en el Servidor (500) - SGC Universitario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="card border-0 shadow-lg p-5 text-center" style="max-width: 520px; border-radius: 1rem;">
        <div class="mb-3">
            <div class="bg-warning-subtle text-warning-emphasis rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="bi bi-exclamation-triangle-fill fs-1"></i>
            </div>
        </div>
        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-1 mb-2 font-monospace align-self-center">ERROR 500</span>
        <h3 class="fw-bold text-dark mb-2">Inconveniente en el Servidor</h3>
        <p class="text-secondary small mb-4">
            Se ha producido una eventualidad técnica interna al procesar su solicitud. La incidencia ha quedado registrada en la bitácora para atención inmediata.
        </p>
        <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm px-4">
                <i class="bi bi-speedometer2 me-1"></i> Regresar al Dashboard
            </a>
        </div>
    </div>
</body>
</html>
