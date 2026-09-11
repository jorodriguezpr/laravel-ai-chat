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
                    <h1>AI Chat Settings</h1>
                    <p class="text-muted">Configure AI responses for your live support chat</p>
                </div>
                <div>
                    <a href="{{ route('admin.live-chat.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Chats
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Status Toggle -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-2">AI Assistant Status</h5>
                            <p class="text-muted small mb-0">Enable AI to respond to visitors when no agent is available</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="form-check form-switch" style="transform: scale(1.5);">
                                <input class="form-check-input" type="checkbox" id="ai-enabled" {{ $settings->enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="ai-enabled"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Settings -->
    <div class="row">
        <!-- AI Behavior Settings -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-brain"></i> AI Behavior
                    </h5>
                </div>
                <div class="card-body">
                    <form id="ai-behavior-form">
                        @csrf
                        <div class="mb-3">
                            <label for="system_prompt" class="form-label">System Prompt</label>
                            <small class="text-muted d-block mb-2">
                                Define how the AI should behave and respond to visitors
                            </small>
                            <textarea
                                class="form-control"
                                id="system_prompt"
                                name="system_prompt"
                                rows="4"
                                required
                                placeholder="Enter system prompt..."
                            >{{ $settings->system_prompt }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="temperature" class="form-label">
                                        Temperature
                                        <span class="badge bg-info">{{ $settings->temperature }}</span>
                                    </label>
                                    <small class="text-muted d-block mb-2">
                                        Lower = focused (0), Higher = creative (2)
                                    </small>
                                    <input
                                        type="range"
                                        class="form-range"
                                        id="temperature"
                                        name="temperature"
                                        min="0"
                                        max="2"
                                        value="{{ $settings->temperature }}"
                                        step="0.1"
                                    >
                                    <small class="text-muted">0 = Deterministic, 2 = Creative</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_tokens" class="form-label">Max Tokens</label>
                                    <small class="text-muted d-block mb-2">
                                        Maximum length of AI responses
                                    </small>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="max_tokens"
                                        name="max_tokens"
                                        value="{{ $settings->max_tokens }}"
                                        min="10"
                                        max="4000"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="save-behavior-btn">
                            <i class="bi bi-check-circle"></i> Save Behavior Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- AI Provider Selection -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-fill"></i> AI Provider
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="provider" class="form-label">Select Provider</label>
                        <select class="form-select" id="provider" required>
                            @foreach($providers as $key => $label)
                                <option value="{{ $key }}" {{ $settings->provider === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle"></i>
                        Changing the provider will automatically update the settings below
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Provider Credentials -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-key"></i> AI Provider Credentials
                    </h5>
                </div>
                <div class="card-body">
                    <div id="provider-credentials">
                        @foreach($providers as $key => $label)
                            @php
                                $credential = $credentials->firstWhere('provider', $key);
                                $hasCredential = $credential !== null;
                            @endphp
                            <div class="provider-card" id="provider-{{ $key }}" style="display: {{ $settings->provider === $key ? 'block' : 'none' }}">
                                <div class="mb-4 p-3 bg-light rounded">
                                    <h6 class="mb-3">
                                        {{ $label }}
                                        @if($hasCredential)
                                            <span class="badge bg-success">Configured</span>
                                        @endif
                                    </h6>
                                    <form class="credential-form" data-provider="{{ $key }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="api-key-{{ $key }}" class="form-label">API Key</label>
                                            <input
                                                type="password"
                                                class="form-control"
                                                id="api-key-{{ $key }}"
                                                placeholder="{{ $hasCredential ? '••••••••••••••••' : 'Enter your ' . $label . ' API key' }}"
                                                {{ $hasCredential ? '' : 'required' }}
                                            >
                                            <small class="text-muted">
                                                {{ $hasCredential ? 'Leave blank to keep existing API key' : 'Your API key is encrypted and never stored in plain text' }}
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="model-{{ $key }}" class="form-label">Model</label>
                                            <select class="form-select" id="model-{{ $key }}" data-saved-model="{{ $credential->model ?? $settings->model ?? '' }}" required>
                                                <option value="">Loading models...</option>
                                            </select>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm" id="save-cred-{{ $key }}">
                                                <i class="bi bi-check-circle"></i> Save & Test Credentials
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm delete-cred" id="delete-cred-{{ $key }}" data-provider="{{ $key }}" style="display: {{ $hasCredential ? 'block' : 'none' }};">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </div>

                                        <div class="status mt-2"></div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <h6 class="mb-2"><i class="bi bi-lightbulb"></i> How to Set Up AI Chat</h6>
                <ol class="mb-0 small">
                    <li><strong>Choose an AI Provider:</strong> Select from OpenAI, Claude, Gemini, or GitHub Models</li>
                    <li><strong>Get API Key:</strong> Sign up and generate an API key from your chosen provider</li>
                    <li><strong>Add Credentials:</strong> Paste your API key and select the model you want to use</li>
                    <li><strong>Test & Save:</strong> Click "Save & Test Credentials" to validate your setup</li>
                    <li><strong>Enable AI:</strong> Toggle the "AI Assistant Status" switch to enable AI responses</li>
                    <li><strong>Configure Behavior:</strong> Set the system prompt, temperature, and token limits</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<style>
    #temperature {
        width: 100%;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const providerSelect = document.getElementById('provider');
    const aiEnabledToggle = document.getElementById('ai-enabled');
    const behaviorForm = document.getElementById('ai-behavior-form');

    // Load models when provider changes
    providerSelect.addEventListener('change', function() {
        loadModelsForProvider(this.value);
        showProviderCard(this.value);
    });

    // Load initial models
    loadModelsForProvider(providerSelect.value);

    // Save behavior settings
    behaviorForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const currentProvider = providerSelect.value;
        const modelSelect = document.getElementById(`model-${currentProvider}`);
        const selectedModel = modelSelect ? modelSelect.value : '';

        if (!selectedModel) {
            showNotification('Please select a model in the Provider Credentials section first', 'error');
            return;
        }

        const data = {
            enabled: aiEnabledToggle.checked,
            provider: currentProvider,
            model: selectedModel,
            system_prompt: formData.get('system_prompt'),
            temperature: parseFloat(formData.get('temperature')),
            max_tokens: parseInt(formData.get('max_tokens')),
        };

        try {
            const response = await fetch('{{ route("admin.ai-settings.update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();
            if (result.success) {
                showNotification('AI settings updated successfully!', 'success');
            } else {
                showNotification('Failed to update settings', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error updating settings', 'error');
        }
    });

    // Handle credential forms
    document.querySelectorAll('.credential-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const provider = this.getAttribute('data-provider');
            const apiKey = document.getElementById(`api-key-${provider}`).value;
            const model = document.getElementById(`model-${provider}`).value;
            const statusEl = this.querySelector('.status');

            statusEl.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

            try {
                const response = await fetch('{{ route("admin.ai-settings.save-credentials") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        provider,
                        api_key: apiKey,
                        model,
                    }),
                });

                const result = await response.json();
                if (result.success) {
                    statusEl.innerHTML = '<div class="alert alert-success small mb-0">✓ Credentials saved and validated!</div>';
                    showNotification('Provider credentials saved successfully!', 'success');
                    document.getElementById(`delete-cred-${provider}`).style.display = 'block';

                    // Update provider selection
                    providerSelect.value = provider;
                } else {
                    statusEl.innerHTML = `<div class="alert alert-danger small mb-0">✗ ${result.message}</div>`;
                }
            } catch (error) {
                console.error('Error:', error);
                statusEl.innerHTML = '<div class="alert alert-danger small mb-0">✗ Error saving credentials</div>';
            }
        });
    });

    // Handle delete credential buttons
    document.querySelectorAll('.delete-cred').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();

            const provider = this.getAttribute('data-provider');
            if (!confirm(`Are you sure you want to delete ${provider} credentials?`)) {
                return;
            }

            try {
                const response = await fetch('{{ route("admin.ai-settings.delete-credentials") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        provider,
                    }),
                });

                const result = await response.json();
                if (result.success) {
                    showNotification('Credentials deleted successfully!', 'success');
                    this.style.display = 'none';
                    // Clear form
                    const form = document.querySelector(`.credential-form[data-provider="${provider}"]`);
                    if (form) {
                        form.reset();
                        form.querySelector('.status').innerHTML = '';
                    }
                } else {
                    showNotification('Failed to delete credentials', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error deleting credentials', 'error');
            }
        });
    });

    // Load models for provider
    async function loadModelsForProvider(provider) {
        document.querySelectorAll('.provider-card').forEach(card => {
            card.style.display = 'none';
        });
        document.getElementById(`provider-${provider}`).style.display = 'block';

        try {
            const response = await fetch(`{{ route('admin.ai-settings.models') }}?provider=${provider}`);
            const result = await response.json();

            if (result.success) {
                const modelSelect = document.getElementById(`model-${provider}`);
                const savedModel = modelSelect.dataset.savedModel || '';

                modelSelect.innerHTML = '';
                Object.entries(result.models).forEach(([key, label]) => {
                    const option = document.createElement('option');
                    option.value = key;
                    option.textContent = label;
                    if (savedModel && key === savedModel) {
                        option.selected = true;
                    }
                    modelSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading models:', error);
        }
    }

    // Show/hide provider card
    function showProviderCard(provider) {
        document.querySelectorAll('.provider-card').forEach(card => {
            card.style.display = 'none';
        });
        document.getElementById(`provider-${provider}`).style.display = 'block';
    }

    // Show notification
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; width: 400px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 4000);
    }
});
</script>
@endsection
