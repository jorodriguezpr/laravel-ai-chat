{{-- @author Jose Rodriguez <jrpcone@gmail.com> --}}
{{-- @license MIT --}}
{{-- @link https://github.com/jorodriguezpr/ --}}

@extends('chat-widget::layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Chat with {{ $chat->visitor_name }}</h1>
                    <p class="text-muted">
                        Email: {{ $chat->visitor_email }} |
                        Status: <span class="badge bg-info">{{ ucfirst($chat->status) }}</span>
                        @php
                            $hasAI = $messages->where('sender_type', 'ai')->count();
                            $hasAgent = $messages->where('sender_type', 'agent')->count();
                            $visitorCount = $messages->where('sender_type', 'visitor')->count();
                        @endphp
                        @if($hasAI > 0)
                            <span class="badge bg-info"><i class="bi bi-robot"></i> {{ $hasAI }} AI Response(s)</span>
                        @endif
                        @if($hasAgent > 0)
                            <span class="badge bg-success"><i class="bi bi-chat-dots"></i> {{ $hasAgent }} Agent Message(s)</span>
                        @endif
                        @if($hasAI > 0 && $hasAgent === 0)
                            <span class="badge bg-warning text-dark"><i class="bi bi-info-circle"></i> AI-Assisted Only</span>
                        @endif
                    </p>
                </div>
                <div>
                    @if($chat->status === 'pending')
                        <button class="btn btn-success btn-lg" id="pickup-chat-btn">
                            <i class="bi bi-hand-thumbs-up"></i> Pickup Chat
                        </button>
                    @endif
                    <button class="btn btn-outline-primary" id="export-csv-btn" title="Export conversation to CSV">
                        <i class="bi bi-download"></i> Export CSV
                    </button>
                    <a href="{{ route('admin.live-chat.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Chats
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="row mb-3">
        <div class="col-md-12">
            <ul class="nav nav-tabs" id="conversationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="chat-tab" data-bs-toggle="tab" data-bs-target="#chat-view" type="button" role="tab">
                        <i class="bi bi-chat-dots"></i> Chat View
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit-log" type="button" role="tab">
                        <i class="bi bi-table"></i> Audit Log
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content" id="conversationTabsContent">
        <!-- Chat View Tab -->
        <div class="tab-pane fade show active" id="chat-view" role="tabpanel">
            <div class="row">
                <div class="col-md-12">
                    <div class="card h-100" style="min-height: 600px;">
                        <!-- Chat Messages -->
                        <div class="card-body p-3" id="chat-messages" style="height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                            @forelse($messages as $message)
                                <div class="mb-3" data-msg-id="{{ $message->id }}">
                                    @if($message->sender_type === 'system')
                                        <div class="text-center">
                                            <small class="text-muted" style="font-style: italic;">{{ $message->message }}</small>
                                        </div>
                                    @elseif($message->sender_type === 'visitor')
                                        <div class="d-flex justify-content-start mb-2">
                                            <div class="p-2 rounded" style="max-width: 70%; background-color: #e9ecef;">
                                                <p class="mb-1">{{ $message->message }}</p>
                                                <small class="text-muted">{{ $message->created_at->format('H:i') }}</small>
                                            </div>
                                        </div>
                                    @elseif($message->sender_type === 'ai')
                                        <div class="d-flex justify-content-end mb-2">
                                            <div class="p-2 rounded" style="max-width: 70%; background-color: #e3f2fd; color: #0c2d79; border-left: 4px solid #2196F3;">
                                                <p class="mb-1"><strong>🤖 AI:</strong> {{ $message->message }}</p>
                                                <small style="color: #0c2d79;">{{ $message->created_at->format('H:i') }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex justify-content-end mb-2">
                                            <div class="p-2 rounded" style="max-width: 70%; background-color: #007bff; color: white;">
                                                <p class="mb-1"><strong>👤 Agent:</strong> {{ $message->message }}</p>
                                                <small style="color: rgba(255,255,255,0.8);">{{ $message->created_at->format('H:i') }}</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center text-muted p-4">
                                    <p>No messages yet</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Message Input -->
                        @if($chat->status === 'pending')
                            <div class="card-footer bg-warning">
                                <div class="text-center p-2">
                                    <p class="mb-2"><strong>This chat is in pending status</strong></p>
                                    <button class="btn btn-success" id="pickup-chat-btn-footer">
                                        <i class="bi bi-hand-thumbs-up"></i> Pickup Chat to Start Responding
                                    </button>
                                </div>
                            </div>
                        @elseif($chat->status !== 'closed')
                            <div class="card-footer">
                                <form id="agent-message-form">
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="agent-message-input"
                                            placeholder="Type your response..."
                                            autocomplete="off"
                                            required
                                        >
                                        <button class="btn btn-primary" type="submit" id="agent-send-btn">
                                            <i class="bi bi-send"></i> Send
                                        </button>
                                        <button class="btn btn-danger" type="button" id="close-chat-btn">
                                            <i class="bi bi-x"></i> Close Chat
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="card-footer bg-warning">
                                <p class="mb-0 text-center">This chat has been closed.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Log Tab -->
        <div class="tab-pane fade" id="audit-log" role="tabpanel">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-table"></i> Conversation Audit Trail
                                <span class="badge bg-secondary float-end">{{ count($messages) }} Total Messages</span>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 160px;">Timestamp</th>
                                            <th style="width: 120px;">Sender</th>
                                            <th style="width: 80px;">Type</th>
                                            <th>Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($messages as $message)
                                            <tr>
                                                <td>
                                                    <small class="text-muted">{{ $message->created_at->format('Y-m-d H:i:s') }}</small>
                                                </td>
                                                <td>
                                                    @if($message->sender_type === 'visitor')
                                                        <span class="badge bg-secondary">Visitor</span>
                                                    @elseif($message->sender_type === 'ai')
                                                        <span class="badge bg-info"><i class="bi bi-robot"></i> AI</span>
                                                    @elseif($message->sender_type === 'agent')
                                                        <span class="badge bg-success"><i class="bi bi-person"></i> Agent</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">System</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ ucfirst($message->sender_type) }}</small>
                                                </td>
                                                <td>
                                                    <div style="max-height: 100px; overflow-y: auto;">
                                                        <small>{{ Str::limit($message->message, 300) }}</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted p-5">
                                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                                    <p class="mt-2">No messages in this conversation</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Conversation Summary -->
                    @php
                        $conversationDuration = $chat->created_at->diffInMinutes($chat->updated_at ?? now());
                        $firstMessage = $messages->first();
                        $lastMessage = $messages->last();
                    @endphp
                    <div class="card border-0 bg-light mt-3">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-info-circle"></i> Conversation Summary</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted">Started</small>
                                    <p class="mb-0"><strong>{{ $chat->created_at->format('Y-m-d H:i:s') }}</strong></p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Duration</small>
                                    <p class="mb-0"><strong>{{ $conversationDuration }} minutes</strong></p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Total Messages</small>
                                    <p class="mb-0">
                                        <strong>
                                            {{ count($messages) }}
                                            @if($hasAI > 0 || $hasAgent > 0)
                                                ({{ $visitorCount }} visitor, {{ $hasAI }} AI, {{ $hasAgent }} agent)
                                            @endif
                                        </strong>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Status</small>
                                    <p class="mb-0">
                                        <span class="badge
                                            @if($chat->status === 'pending') bg-warning text-dark
                                            @elseif($chat->status === 'active') bg-success
                                            @else bg-secondary
                                            @endif
                                        ">
                                            {{ ucfirst($chat->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #chat-messages {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }

    .table td {
        vertical-align: top;
        word-break: break-word;
    }
</style>

<script>
console.log('Agent chat page loaded');

// CSRF token from meta tag
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

// Export to CSV
document.getElementById('export-csv-btn').addEventListener('click', function() {
    const rows = document.querySelectorAll('table tbody tr');
    let csv = '"Timestamp","Sender","Type","Message"\n';

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 4) {
            const timestamp = cells[0].textContent.trim();
            const sender = cells[1].textContent.trim();
            const type = cells[2].textContent.trim();
            const message = cells[3].textContent.trim().replace(/"/g, '""'); // Escape quotes

            csv += `"${timestamp}","${sender}","${type}","${message}"\n`;
        }
    });

    // Create blob and download
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `chat_${new Date().toISOString().split('T')[0]}_{{ $chat->id }}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});

// Send message as agent
const messageForm = document.getElementById('agent-message-form');
if (messageForm) {
    messageForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const message = document.getElementById('agent-message-input').value.trim();
        if (!message) return;

        try {
            const csrfToken = getCsrfToken();
            console.log('Sending message to: /admin/live-chat/{{ $chat->id }}/message');

            const response = await fetch('/admin/live-chat/{{ $chat->id }}/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ message: message }),
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Server error response:', errorText.substring(0, 500));
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                document.getElementById('agent-message-input').value = '';

                // Add message to chat
                const messagesDiv = document.getElementById('chat-messages');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'mb-3';
                messageDiv.setAttribute('data-msg-id', data.message.id);
                messageDiv.innerHTML = `
                    <div class="d-flex justify-content-end mb-2">
                        <div class="p-2 rounded" style="max-width: 70%; background-color: #007bff; color: white;">
                            <p class="mb-1"><strong>👤 Agent:</strong> ${data.message.message}</p>
                            <small style="color: rgba(255,255,255,0.8);">${new Date(data.message.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</small>
                        </div>
                    </div>
                `;
                messagesDiv.appendChild(messageDiv);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error sending message:', error.message);
            alert('Failed to send message: ' + error.message);
        }
    });
}

// Close chat button
const closeChatBtn = document.getElementById('close-chat-btn');
if (closeChatBtn) {
    closeChatBtn.addEventListener('click', async () => {
        if (confirm('Are you sure you want to close this chat?')) {
            try {
                const response = await fetch('/admin/live-chat/{{ $chat->id }}/close', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server error:', errorText);
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    alert('Chat closed');
                    window.location.href = '/admin/live-chat';
                } else {
                    alert('Error: ' + (data.error || 'Failed to close chat'));
                }
            } catch (error) {
                console.error('Error closing chat:', error.message);
                alert('Failed to close chat: ' + error.message);
            }
        }
    });
}

// Poll for new messages every 2 seconds
let lastCheck = new Date().toISOString();

setInterval(async () => {
    try {
        const response = await fetch(`/admin/live-chat/api/{{ $chat->id }}/messages?since=${lastCheck}`, {
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
            },
        });

        const data = await response.json();

        if (data.success && data.messages.length > 0) {
            const messagesDiv = document.getElementById('chat-messages');

            data.messages.forEach(msg => {
                const existing = messagesDiv.querySelector(`[data-msg-id="${msg.id}"]`);
                if (!existing) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'mb-3';
                    messageDiv.setAttribute('data-msg-id', msg.id);

                    if (msg.sender_type === 'visitor') {
                        messageDiv.innerHTML = `
                            <div class="d-flex justify-content-start mb-2">
                                <div class="p-2 rounded" style="max-width: 70%; background-color: #e9ecef;">
                                    <p class="mb-1">${msg.message}</p>
                                    <small class="text-muted">${new Date(msg.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</small>
                                </div>
                            </div>
                        `;
                    } else if (msg.sender_type === 'system') {
                        messageDiv.innerHTML = `
                            <div class="text-center">
                                <small class="text-muted" style="font-style: italic;">${msg.message}</small>
                            </div>
                        `;
                    } else if (msg.sender_type === 'ai') {
                        messageDiv.innerHTML = `
                            <div class="d-flex justify-content-end mb-2">
                                <div class="p-2 rounded" style="max-width: 70%; background-color: #e3f2fd; color: #0c2d79; border-left: 4px solid #2196F3;">
                                    <p class="mb-1"><strong>🤖 AI:</strong> ${msg.message}</p>
                                    <small style="color: #0c2d79;">${new Date(msg.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</small>
                                </div>
                            </div>
                        `;
                    }

                    messagesDiv.appendChild(messageDiv);
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                    lastCheck = msg.created_at;
                }
            });
        }
    } catch (error) {
        console.error('Error polling messages:', error);
    }
}, 2000);

// Scroll to bottom on load
document.addEventListener('DOMContentLoaded', () => {
    const messagesDiv = document.getElementById('chat-messages');
    if (messagesDiv) {
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
});

// Pickup chat button handlers
const pickupBtn = document.getElementById('pickup-chat-btn');
const pickupBtnFooter = document.getElementById('pickup-chat-btn-footer');

async function handlePickup() {
    try {
        const response = await fetch('/admin/live-chat/{{ $chat->id }}/pickup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            // Reload the page to show the updated status and message form
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to pickup chat'));
        }
    } catch (error) {
        console.error('Error picking up chat:', error.message);
        alert('Failed to pickup chat: ' + error.message);
    }
}

if (pickupBtn) {
    pickupBtn.addEventListener('click', handlePickup);
}

if (pickupBtnFooter) {
    pickupBtnFooter.addEventListener('click', handlePickup);
}
</script>
@endsection
