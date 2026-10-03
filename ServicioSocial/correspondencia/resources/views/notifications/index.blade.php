@extends('layouts.app')

@section('title', 'Notificaciones del Sistema')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-bell-fill text-primary me-2"></i>Notificaciones del Sistema
        </h3>
        <p class="text-secondary mb-0">Alertas de asignación de correspondencia, cambios de estado y avisos de términos.</p>
    </div>
    @if($unreadCount > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-check2-all me-1"></i> Marcar todas como leídas ({{ $unreadCount }})
            </button>
        </form>
    @endif
</div>

<div class="card card-custom p-3 mb-4">
    <div class="d-flex gap-2">
        <a href="{{ route('notifications.index') }}" class="btn {{ !request('unread') ? 'btn-primary' : 'btn-light border' }} btn-sm">
            Todas las notificaciones
        </a>
        <a href="{{ route('notifications.index', ['unread' => 1]) }}" class="btn {{ request('unread') ? 'btn-primary' : 'btn-light border' }} btn-sm">
            Solo no leídas ({{ $unreadCount }})
        </a>
    </div>
</div>

<div class="card card-custom overflow-hidden">
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            <div class="list-group-item p-3 {{ $notification->read_at ? 'bg-white' : 'bg-primary-subtle bg-opacity-10 border-start border-4 border-primary' }}">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-3 bg-light fs-4 text-center" style="width: 45px; height: 45px;">
                            <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }}"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="fw-bold mb-0 text-dark">{{ $notification->data['title'] ?? 'Aviso del Sistema' }}</h6>
                                @if(!$notification->read_at)
                                    <span class="badge bg-primary px-2 py-0" style="font-size: 0.68rem;">NUEVA</span>
                                @endif
                            </div>
                            <p class="text-secondary mb-1 small">{{ $notification->data['message'] ?? '' }}</p>
                            <small class="text-muted font-monospace">
                                <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('d/m/Y H:i') }})
                            </small>
                        </div>
                    </div>

                    <div class="d-flex gap-2 align-items-center align-self-end align-self-md-center">
                        @if(isset($notification->data['url']))
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Ir al Documento
                                </button>
                            </form>
                        @elseif(!$notification->read_at)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-light border btn-sm">
                                    <i class="bi bi-check me-1"></i> Marcar Leída
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-1 d-block mb-2 text-secondary"></i>
                No tiene notificaciones registradas.
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="card-footer bg-white border-top px-4 py-3">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
