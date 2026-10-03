<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inicio') - {{ config('app.name', 'Gestión de Correspondencia') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-navy: #1e3a8a;
            --primary-navy-dark: #0f172a;
            --accent-blue: #3b82f6;
            --body-bg: #f8fafc;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: #1e293b;
            min-height: 100vh;
        }
        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #cbd5e1;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #ffffff;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .sidebar-brand-icon {
            width: 38px;
            height: 38px;
            background: #2563eb;
            color: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .nav-section-title {
            padding: 1rem 1.5rem 0.35rem;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }
        .sidebar-nav {
            list-style: none;
            padding: 0.5rem 0.75rem;
            margin: 0;
        }
        .sidebar-nav .nav-item {
            margin-bottom: 0.25rem;
        }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.85rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .sidebar-nav .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }
        .sidebar-nav .nav-link.active {
            color: #ffffff;
            background: #2563eb;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }
        .sidebar-nav .nav-link i {
            font-size: 1.15rem;
        }
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 900;
        }
        .page-content {
            padding: 1.5rem;
            flex-grow: 1;
        }
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }
        .badge-role {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35em 0.65em;
            border-radius: 6px;
        }
        .pagination {
            margin-bottom: 0;
            gap: 0.25rem;
        }
        .pagination .page-link {
            border-radius: 6px !important;
            color: #334155;
            font-size: 0.85rem;
            padding: 0.35rem 0.65rem;
            border-color: #e2e8f0;
        }
        .pagination .page-item.active .page-link {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <div class="fw-bold fs-6 lh-1">SGC Universitario</div>
                <small class="text-secondary" style="font-size: 0.75rem;">Gestión de Correspondencia</small>
            </div>
        </a>

        <div class="nav-section-title">Principal</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-bell-fill"></i>
                    <span>Notificaciones</span>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge bg-danger ms-auto">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </a>
            </li>
        </ul>

        @if(auth()->user()->isAdmin())
        <div class="nav-section-title">Administración</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Roles y Permisos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('audit-logs.index') }}" class="nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i>
                    <span>Auditoría Forense</span>
                </a>
            </li>
        </ul>
        @endif

        <div class="nav-section-title">Catálogos Institucionales</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>Departamentos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('document-types.index') }}" class="nav-link {{ request()->routeIs('document-types.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Tipos de Documento</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('senders.index') }}" class="nav-link {{ request()->routeIs('senders.*') ? 'active' : '' }}">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Remitentes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('recipients.index') }}" class="nav-link {{ request()->routeIs('recipients.*') ? 'active' : '' }}">
                    <i class="bi bi-send-check"></i>
                    <span>Destinatarios</span>
                </a>
            </li>
        </ul>

        <div class="nav-section-title">Correspondencia Institucional</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('received-documents.index') }}" class="nav-link {{ request()->routeIs('received-documents.*') ? 'active' : '' }}">
                    <i class="bi bi-inbox-fill"></i>
                    <span>Correspondencia Recibida</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('sent-documents.index') }}" class="nav-link {{ request()->routeIs('sent-documents.*') ? 'active' : '' }}">
                    <i class="bi bi-send-fill"></i>
                    <span>Correspondencia Enviada</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('expedients.index') }}" class="nav-link {{ request()->routeIs('expedients.*') ? 'active' : '' }}">
                    <i class="bi bi-folder2-open"></i>
                    <span>Expedientes Digitales</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('search.advanced') }}" class="nav-link {{ request()->routeIs('search.*') ? 'active' : '' }}">
                    <i class="bi bi-search"></i>
                    <span>Búsqueda Avanzada</span>
                </a>
            </li>
        </ul>

        <div class="nav-section-title">Seguimiento y Control</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('tracking.my-documents') }}" class="nav-link {{ request()->routeIs('tracking.my-documents') ? 'active' : '' }}">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Mis Asignaciones</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tracking.pending') }}" class="nav-link {{ request()->routeIs('tracking.pending') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Pendientes y Trámites</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tracking.expired') }}" class="nav-link {{ request()->routeIs('tracking.expired') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    <span>Documentos Vencidos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Reportes y Estadísticas</span>
                </a>
            </li>
        </ul>
    </aside>

    <div class="main-wrapper">
        <header class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle" type="button">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-lg-block">
                    <span class="text-secondary fs-6 fw-medium">Sistema de Gestión de Correspondencia Institucional</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <form action="{{ route('search.advanced') }}" method="GET" class="d-none d-md-flex align-items-center" style="width: 260px;">
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control bg-light border-secondary-subtle" placeholder="Buscar folio, asunto..." value="{{ request('q') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <div class="dropdown">
                    @php
                        $unreadNotifications = auth()->user()->unreadNotifications;
                        $unreadCount = $unreadNotifications->count();
                        $recentNotifications = auth()->user()->notifications()->take(5)->get();
                    @endphp
                    <button class="btn btn-light position-relative border rounded-circle d-flex align-items-center justify-content-center p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 38px; height: 38px;" title="Notificaciones">
                        <i class="bi bi-bell fs-5 text-secondary"></i>
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.65rem;">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                <span class="visually-hidden">mensajes no leídos</span>
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg py-0" style="width: 360px; max-width: 90vw; border-radius: 0.75rem; overflow: hidden;">
                        <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark fs-6">Notificaciones</span>
                                @if($unreadCount > 0)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $unreadCount }} nueva(s)</span>
                                @endif
                            </div>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 text-primary text-decoration-none small" style="font-size: 0.75rem;">Marcar todo leído</button>
                                </form>
                            @endif
                        </div>
                        <div class="list-group list-group-flush" style="max-height: 360px; overflow-y: auto;">
                            @forelse($recentNotifications as $notification)
                                <div class="list-group-item list-group-item-action p-3 {{ $notification->read_at ? 'opacity-75' : 'bg-primary-subtle bg-opacity-10' }}">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="rounded-circle p-2 bg-light border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                                            <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }} fs-6"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="fw-semibold text-dark text-truncate" style="font-size: 0.82rem;">{{ $notification->data['title'] ?? 'Notificación' }}</span>
                                                <small class="text-muted text-nowrap ms-2" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="text-secondary mb-1" style="font-size: 0.78rem; line-height: 1.3;">{{ $notification->data['message'] ?? '' }}</p>
                                            <div class="d-flex align-items-center gap-2 mt-2">
                                                @if(!empty($notification->data['url']))
                                                    <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.72rem;">Ver</a>
                                                @endif
                                                @if(is_null($notification->read_at))
                                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-light border py-0 px-2 text-muted" style="font-size: 0.72rem;">Marcar leída</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    <i class="bi bi-bell-slash fs-2 mb-2 d-block text-secondary opacity-50"></i>
                                    <p class="mb-0 small">No tienes notificaciones registradas</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-2 border-top bg-light text-center">
                            <a href="{{ route('notifications.index') }}" class="text-decoration-none small fw-semibold text-primary">Ver todas las notificaciones</a>
                        </div>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border px-3 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="text-start d-none d-md-block">
                            <div class="fw-semibold text-dark lh-1" style="font-size: 0.85rem;">{{ auth()->user()->name }}</div>
                            <small class="text-muted" style="font-size: 0.72rem;">
                                {{ auth()->user()->roles->first()?->label ?? 'Sin Rol' }}
                            </small>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('users.show', auth()->user()) }}">
                                <i class="bi bi-person me-2 text-primary"></i> Mi Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Por favor corrija los siguientes errores:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    @yield('scripts')
</body>
</html>
