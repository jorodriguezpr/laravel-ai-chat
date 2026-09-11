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
                    <h1>Live Chat Settings</h1>
                    <p class="text-muted">Configure the visitor-facing chat experience</p>
                </div>
                <div>
                    <a href="{{ route('admin.live-chat.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Chats
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Welcome Message</h5>
                    <p class="text-muted small">Shown automatically to visitors when they start a new chat.</p>

                    <form id="live-chat-settings-form">
                        @csrf
                        <div class="mb-3">
                            <textarea name="welcome_message" id="welcome_message" class="form-control" rows="4"
                                maxlength="1000">{{ $welcomeMessage }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2"></i> Save Settings
                        </button>
                        <span id="save-status" class="ms-3 text-success" style="display: none;">
                            <i class="bi bi-check-circle"></i> Saved
                        </span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('live-chat-settings-form').addEventListener('submit', function (e) {
    e.preventDefault();

    fetch('{{ route('admin.live-chat.settings.update') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            welcome_message: document.getElementById('welcome_message').value,
        }),
    })
        .then(r => r.json())
        .then(data => {
            const status = document.getElementById('save-status');
            if (data.success) {
                status.style.display = 'inline';
                setTimeout(() => { status.style.display = 'none'; }, 2000);
            } else {
                alert('Error: ' + (data.message || 'Failed to save settings'));
            }
        })
        .catch(error => {
            console.error('Error saving live chat settings:', error);
            alert('Failed to save settings: ' + error.message);
        });
});
</script>
@endsection
