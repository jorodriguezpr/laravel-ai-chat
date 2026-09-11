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
                    <h1>Live Chat Management</h1>
                    <p class="text-muted">Manage customer support chats</p>
                </div>
                <div>
                    <a href="{{ route('admin.live-chat.settings') }}" class="btn btn-outline-primary">
                        <i class="bi bi-sliders"></i> Chat Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="row mb-3">
        <div class="col-md-12">
            <ul class="nav nav-tabs" id="chatTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="bi bi-clock-history"></i> Pending
                        <span class="badge bg-danger ms-2">{{ count($pendingChats) }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                        <i class="bi bi-chat-dots"></i> Active
                        <span class="badge bg-success ms-2">{{ count($activeChats) }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab">
                        <i class="bi bi-archive"></i> Past Conversations
                        <span class="badge bg-secondary ms-2">{{ count($closedChats) }}</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="chatTabsContent">
        <!-- Pending Chats Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if(count($pendingChats) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($pendingChats as $chat)
                                <a href="{{ route('admin.live-chat.show', $chat->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $chat->visitor_name }}</h6>
                                            <small class="text-muted">{{ $chat->visitor_email }}</small>
                                            <br>
                                            <small class="text-muted">Started {{ $chat->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-warning text-dark">{{ count($chat->messages) }} messages</span>
                                            <br>
                                            <small class="badge bg-info">Pending</small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2">No pending chats</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Active Chats Tab -->
        <div class="tab-pane fade" id="active" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if(count($activeChats) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($activeChats as $chat)
                                <a href="{{ route('admin.live-chat.show', $chat->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $chat->visitor_name }}</h6>
                                            <small class="text-muted">{{ $chat->visitor_email }}</small>
                                            <br>
                                            <small class="text-muted">Last activity {{ $chat->updated_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success">{{ count($chat->messages) }} messages</span>
                                            <br>
                                            <small class="badge bg-success">Active</small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-chat-left" style="font-size: 2rem;"></i>
                            <p class="mt-2">No active chats</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Past Conversations Tab -->
        <div class="tab-pane fade" id="past" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if(count($closedChats) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($closedChats as $chat)
                                @php
                                    $hasAI = $chat->messages()->where('sender_type', 'ai')->exists();
                                    $hasAgent = $chat->messages()->where('sender_type', 'agent')->exists();
                                @endphp
                                <a href="{{ route('admin.live-chat.show', $chat->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $chat->visitor_name }}</h6>
                                            <small class="text-muted">{{ $chat->visitor_email }}</small>
                                            <br>
                                            <small class="text-muted">Closed {{ $chat->updated_at->diffForHumans() }}</small>
                                            <br>
                                            @if($hasAI)
                                                <span class="badge bg-info mt-2"><i class="bi bi-robot"></i> Had AI Interaction</span>
                                            @endif
                                            @if($hasAgent)
                                                <span class="badge bg-success mt-2"><i class="bi bi-chat-dots"></i> Had Agent</span>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-secondary">{{ count($chat->messages) }} messages</span>
                                            <br>
                                            <small class="badge bg-secondary">Closed</small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-archive" style="font-size: 2rem;"></i>
                            <p class="mt-2">No past conversations</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h6>💡 How to use:</h6>
                <ul class="mb-0">
                    <li><strong>Pending:</strong> New visitors waiting for support</li>
                    <li><strong>Active:</strong> Ongoing conversations with visitors</li>
                    <li><strong>Past Conversations:</strong> View all closed and completed chats</li>
                    <li>Click on any chat to view the full conversation history and details</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
