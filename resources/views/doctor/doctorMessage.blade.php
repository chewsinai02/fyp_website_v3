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
                                
                                // Get all unread messages from this conversation
                                $unreadCount = \App\Models\Message::where('sender_id', $displayUser->id)
                                    ->where('receiver_id', auth()->id())
                                    ->where('is_read', 1)
                                    ->count();
                                    
                                // Message is considered unread if there are any unread messages in the conversation
                                $isUnread = $unreadCount > 0;
                            @endphp
                            
                            <a href="{{ route('doctor.chat', $displayUser->id) }}" 
                               class="message-item position-relative {{ $isUnread ? 'unread-message' : 'read-message' }}"
                               data-href="{{ route('doctor.chat', $displayUser->id) }}"
                               onclick="markAsRead({{ $displayUser->id }}, event, this)"
                               style="display: block; padding: 1rem; text-decoration: none; color: inherit;">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        <img src="{{ asset($displayUser->profile_picture ?? 'images/profile.png') }}"
                                             alt="{{ $displayUser->name }}"
                                             class="rounded-circle shadow-sm"
                                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #fff;">
                                        @if($isUnread)
                                            <span class="unread-indicator"></span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-truncate {{ $isUnread ? 'fw-bold' : '' }}">
                                                {{ $displayUser->name }}
                                            </h6>
                                            <div class="d-flex align-items-center">
                                                @if($unreadCount > 0)
                                                    <span class="badge bg-primary rounded-pill me-2">{{ $unreadCount }}</span>
                                                @endif
                                                <small class="text-muted">{{ $message->created_at->format('h:i A') }}</small>
                                            </div>
                                        </div>
                                        <p class="message-preview {{ $isUnread ? 'unread-text' : 'read-text' }}">
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

<style>
/* Message item base styles */
.message-item {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f1f5f9;
}

/* Unread message styles */
.unread-message {
    background-color: #EBF8FF !important;
    border-left: 4px solid #3B82F6;
}

.unread-text {
    color: #1e293b !important;
    font-weight: 600;
    font-size: 0.875rem;
}

/* Read message styles */
.read-message {
    background-color: white;
    border-left: 4px solid transparent;
}

.read-text {
    color: #64748b !important;
    font-weight: normal;
    font-size: 0.875rem;
}

/* Unread indicator dot */
.unread-indicator {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 12px;
    height: 12px;
    background-color: #3B82F6;
    border-radius: 50%;
    border: 2px solid white;
}

/* Message preview base styles */
.message-preview {
    margin: 0;
    line-height: 1.5;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}

/* Hover effects */
.message-item:hover {
    background-color: #F1F5F9 !important;
}

/* Delete button */
.delete-message {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.message-item:hover .delete-message {
    opacity: 1;
}

.delete-message .btn-link {
    font-size: 1.1rem;
}

.delete-message .btn-link:hover {
    transform: scale(1.1);
}

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

/* Media Query */
@media (max-width: 768px) {
    .message-list {
        height: calc(50vh - 100px) !important;
        margin-bottom: 1rem;
    }
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    font-weight: 600;
}

.badge.bg-primary {
    background-color: #3B82F6 !important;
}

.badge.rounded-pill {
    min-width: 1.5rem;
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

// Function to handle marking messages as read
function markAsRead(senderId, event, element) {
    event.preventDefault(); // Prevent the default link behavior
    
    const chatUrl = element.getAttribute('data-href');
    
    fetch(`/doctor/mark-messages-read/${senderId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to chat page after marking as read
            window.location.href = chatUrl;
        }
    })
    .catch(error => {
        console.error('Error marking messages as read:', error);
        // Still redirect even if there's an error
        window.location.href = chatUrl;
    });
}
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
