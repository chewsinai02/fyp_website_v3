@extends('doctor.layout')
@section('title', 'Chat')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="background: linear-gradient(45deg, #2C3E50, #3498DB); -webkit-background-clip: text; -webkit-text-fill-color: transparent;" class="fs-1 mb-2">Chat</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-chat-dots me-2"></i>
                Conversation with {{ $patient->name }}
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="height: calc(100vh - 200px); display: flex; flex-direction: column; border-radius: 12px; overflow: hidden;">
        <!-- Chat Header -->
        <div style="background-color: #fff;" class="p-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <img src="{{ asset($patient->profile_picture ?? 'images/profile.png') }}" 
                         alt="{{ $patient->name }}" 
                         class="rounded-circle shadow-sm"
                         style="width: 45px; height: 45px; object-fit: cover;">
                </div>
                <div>
                    <h6 class="mb-0">{{ $patient->name }}</h6>
                    <small class="text-muted">Patient</small>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chatContainer" style="flex: 1; overflow-y: auto; background-color: #f8fafc;" class="p-4">
            @foreach($messages as $message)
                <div style="margin-bottom: 1rem; display: flex; justify-content: {{ $message->sender_id == auth()->id() ? 'flex-end' : 'flex-start' }}">
                    <div style="max-width: 70%;">
                        <div style="padding: 0.75rem 1rem; border-radius: 12px; margin-bottom: 0.25rem; position: relative; 
                                  {{ $message->sender_id == auth()->id() 
                                     ? 'background-color: var(--bs-primary); color: white; border-bottom-right-radius: 4px;' 
                                     : 'background-color: white; border-bottom-left-radius: 4px;' }}">
                            @if($message->image)
                                <div class="mb-2">
                                    <img src="{{ asset($message->image) }}" 
                                         alt="Sent image" 
                                         style="max-width: 100%; border-radius: 8px; cursor: pointer;"
                                         onclick="window.open('{{ asset($message->image) }}', '_blank')">
                                </div>
                            @endif
                            @if($message->message && $message->message !== '[Image]')
                                {{ $message->message }}
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #64748b; 
                                  {{ $message->sender_id == auth()->id() ? 'justify-content: flex-end;' : '' }}">
                            <small>{{ $message->created_at->format('h:i A') }}</small>
                            @if($message->sender_id == auth()->id())
                                <small><i class="bi bi-check2-all"></i></small>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chat Input -->
        <div class="border-top p-3">
            <form action="{{ route('chat.store', ['receiverId' => $patient->id]) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="message-form"
                  id="chatForm">
                @csrf
                <div class="input-group">
                    <input type="file" 
                           id="cameraInput" 
                           name="image" 
                           accept="image/*" 
                           capture="environment" 
                           class="d-none"
                           onchange="handleImageUpload(this)">
                    <input type="file" 
                           id="galleryInput" 
                           name="image" 
                           accept="image/*" 
                           class="d-none"
                           onchange="handleImageUpload(this)">
                    
                    <button type="button" 
                            class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; padding: 0; border: 1px solid #e2e8f0;"
                            onclick="document.getElementById('cameraInput').click();">
                        <i class="bi bi-camera"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-light rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; padding: 0; border: 1px solid #e2e8f0;"
                            onclick="document.getElementById('galleryInput').click();">
                        <i class="bi bi-image"></i>
                    </button>
                    
                    <input type="text" 
                           name="message" 
                           id="messageInput"
                           class="form-control"
                           style="border-radius: 50px; padding: 0.75rem 1rem; border: 1px solid #e2e8f0;"
                           placeholder="Type your message...">
                    
                    <button type="submit" 
                            class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; padding: 0;">
                        <i class="bi bi-send"></i>
                    </button>
                </div>

                <!-- Preview container -->
                <div id="imagePreview" class="mt-2 d-none">
                    <div class="position-relative d-inline-block">
                        <img src="" alt="Preview" style="max-height: 100px; border-radius: 8px;">
                        <button type="button" 
                                class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle"
                                style="margin: -8px -8px 0 0;"
                                onclick="removeImage()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Keep only essential styles that can't be inlined -->
<style>
/* Custom Scrollbar - Can't be inlined */
#chatContainer::-webkit-scrollbar {
    width: 6px;
}

#chatContainer::-webkit-scrollbar-track {
    background: #f1f5f9;
}

#chatContainer::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#chatContainer::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Media Query - Can't be inlined */
@media (max-width: 768px) {
    .chat-container {
        height: calc(100vh - 150px) !important;
    }
}

/* Focus state - Can't be inlined */
.form-control:focus {
    box-shadow: none;
    border-color: var(--bs-primary) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.getElementById('chatContainer');
    chatContainer.scrollTop = chatContainer.scrollHeight;
    
    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Disable the submit button to prevent double submission
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            // Don't set Content-Type header - let the browser set it with boundary for multipart/form-data
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Failed to send message');
            }
            return data;
        })
        .then(data => {
            // Clear input and preview
            document.getElementById('messageInput').value = '';
            removeImage();
            
            // Add new message to chat
            const messageHtml = createMessageHtml(data.message);
            chatContainer.insertAdjacentHTML('beforeend', messageHtml);
            
            // Scroll to bottom
            chatContainer.scrollTop = chatContainer.scrollHeight;
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Failed to send message');
        })
        .finally(() => {
            // Re-enable the submit button
            submitButton.disabled = false;
        });
    });
});

function handleImageUpload(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('d-none');
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    const preview = document.getElementById('imagePreview');
    preview.classList.add('d-none');
    preview.querySelector('img').src = '';
    
    // Clear file inputs
    document.getElementById('cameraInput').value = '';
    document.getElementById('galleryInput').value = '';
}

function createMessageHtml(message) {
    const time = new Date(message.created_at).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
    
    let content = '';
    
    // Add image if exists
    if (message.image) {
        content += `
            <div class="mb-2">
                <img src="${message.image}" 
                     alt="Sent image" 
                     style="max-width: 100%; border-radius: 8px; cursor: pointer;"
                     onclick="window.open('${message.image}', '_blank')">
            </div>
        `;
    }
    
    // Add message text if exists
    if (message.message) {
        content += `<div>${message.message}</div>`;
    }
    
    return `
        <div style="margin-bottom: 1rem; display: flex; justify-content: flex-end">
            <div style="max-width: 70%;">
                <div style="padding: 0.75rem 1rem; border-radius: 12px; margin-bottom: 0.25rem; position: relative; 
                          background-color: var(--bs-primary); color: white; border-bottom-right-radius: 4px;">
                    ${content}
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #64748b; justify-content: flex-end;">
                    <small>${time}</small>
                    <small><i class="bi bi-check2-all"></i></small>
                </div>
            </div>
        </div>
    `;
}
</script>
@endsection
