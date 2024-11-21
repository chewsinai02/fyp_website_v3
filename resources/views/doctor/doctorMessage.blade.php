@extends('doctor.layout')
@section('title', 'Messages')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="background: linear-gradient(45deg, #2C3E50, #3498DB); -webkit-background-clip: text; -webkit-text-fill-color: transparent;" class="fs-1 mb-2">Messages</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-chat-dots me-2"></i>
                Your conversations
            </p>
        </div>
    </div>

    <div class="row">
        <!-- Messages List -->
        <div class="col-md-12 messageList">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; height: calc(100vh - 200px); overflow: hidden;">
                <div class="card-body p-0">
                    <div style="height: 100%; overflow-y: auto;">
                        @foreach ($lastMessages as $message)
                            @php
                                $displayUser = $message->sender_id == auth()->id() ? $message->receiver : $message->sender;
                                $messagePreview = $message->sender_id == auth()->id() ? 'Me: ' . Str::limit($message->message, 40) : Str::limit($message->message, 40);
                                $isUnread = !$message->is_read && $message->receiver_id == auth()->id();
                            @endphp
                            
                            <a href="{{ route('doctor.chat', $displayUser->id) }}" 
                               class="message-item position-relative"
                               style="display: block; padding: 1rem; border-bottom: 1px solid #f1f5f9; text-decoration: none; color: inherit; transition: all 0.2s ease; background-color: {{ $isUnread ? 'rgba(var(--bs-primary-rgb), 0.05)' : 'transparent' }}">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        <img src="{{ asset($displayUser->profile_picture ?? 'images/profile.png') }}"
                                             alt="{{ $displayUser->name }}"
                                             class="rounded-circle shadow-sm"
                                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #fff;">
                                        @if($isUnread)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"
                                                  style="width: 10px; height: 10px; padding: 0; border: 2px solid #fff;">
                                                <span class="visually-hidden">unread message</span>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-truncate">{{ $displayUser->name }}</h6>
                                            <small class="text-muted ms-2">{{ $message->created_at->format('h:i A') }}</small>
                                        </div>
                                        <p class="text-muted small mb-0 text-truncate">
                                            {{ $messagePreview }}
                                        </p>
                                    </div>
                                    
                                    <div class="delete-message ms-2" onclick="event.preventDefault();">
                                        <button class="btn btn-link text-danger p-0" 
                                                onclick="confirmDelete({{ $message->id }}, event)"
                                                data-bs-toggle="tooltip" 
                                                title="Delete Conversation">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Keep only these essential styles that can't be inlined -->
<style>
/* Custom Scrollbar */
.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.messages-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Hover effects */
.messageList a:hover {
    background-color: rgba(0, 0, 0, 0.02) !important;
}

/* Media Query */
@media (max-width: 768px) {
    .message-list {
        height: calc(50vh - 100px) !important;
        margin-bottom: 1rem;
    }
}

.message-item:hover .delete-message {
    opacity: 1;
}

.delete-message {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.delete-message .btn-link {
    font-size: 1.1rem;
}

.delete-message .btn-link:hover {
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.getElementById('chatContainer');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
        
        document.querySelector('.message-form')?.addEventListener('submit', function() {
            setTimeout(() => {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }, 100);
        });
    }
});

function confirmDelete(messageId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `{{ url('doctor/messages') }}/${messageId}`;
    modal.show();
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
});
</script>

<!-- Add Modal for Delete Confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this conversation? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
