{{-- @author Jose Rodriguez <jrpcone@gmail.com> --}}
{{-- @license MIT --}}
{{-- @link https://github.com/jorodriguezpr/ --}}

<!-- Chat Widget Bubble and Container -->
<div id="chat-widget-container" class="chat-widget-container">
    <!-- Chat Bubble Button -->
    <button id="chat-bubble-btn" class="chat-bubble-btn" title="Open Chat">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 15c4.418 0 8-1.79 8-4s-3.582-4-8-4-8 1.79-8 4 3.582 4 8 4zm0-1c-3.315 0-6-1.343-6-3s2.685-3 6-3 6 1.343 6 3-2.685 3-6 3z"/>
            <path d="M8 11c3.547 0 6-1.5 6-3V5h.5a.5.5 0 0 0 0-1h-16a.5.5 0 0 0 0 1H2v3c0 1.5 2.453 3 6 3z"/>
        </svg>
        <span class="chat-bubble-badge" id="chat-unread-badge" style="display: none;">0</span>
    </button>

    <!-- Chat Window -->
    <div id="chat-window" class="chat-window" style="display: none;">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-content">
                <h3 class="chat-title">Support Chat</h3>
                <p class="chat-subtitle" id="chat-status">Connecting...</p>
            </div>
            <div class="chat-header-buttons">
                <button id="chat-minimize-btn" class="chat-minimize-btn" title="Minimize">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M12.5 15h-9a1.5 1.5 0 0 1-1.5-1.5V4a.5.5 0 0 1 1 0v9.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V4a.5.5 0 0 1 1 0v9.5a1.5 1.5 0 0 1-1.5 1.5z"/>
                    </svg>
                </button>
                <button id="chat-close-btn" class="chat-close-btn" title="Close Chat" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="chat-messages" id="chat-messages">
            <!-- Loading state -->
            <div class="chat-loading" id="chat-loading">
                <div class="spinner"></div>
                <p>Loading chat...</p>
            </div>
        </div>

        <!-- Initial Form (shown before chat starts) -->
        <div id="chat-init-form" class="chat-init-form">
            <form id="chat-start-form">
                <div class="mb-3">
                    <label for="visitor-name" class="form-label">Your Name</label>
                    <input type="text" class="form-control form-control-sm" id="visitor-name"
                           name="visitor_name" required placeholder="Enter your name">
                </div>
                <div class="mb-3">
                    <label for="visitor-email" class="form-label">Your Email</label>
                    <input type="email" class="form-control form-control-sm" id="visitor-email"
                           name="visitor_email" required placeholder="Enter your email">
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    Start Chat
                </button>
            </form>
        </div>

        <!-- AI Status and Actions -->
        <div class="chat-ai-actions" id="chat-ai-actions" style="display: none;">
            <button type="button" class="btn btn-outline-secondary btn-sm w-100" id="chat-talk-to-human-btn">
                <i class="bi bi-person"></i> Talk to Human Agent
            </button>
        </div>

        <!-- Message Input (shown after chat starts) -->
        <div class="chat-input-area" id="chat-input-area" style="display: none;">
            <form id="chat-message-form">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="chat-message-input"
                           placeholder="Type a message..." autocomplete="off">
                    <button class="btn btn-primary" type="submit" id="chat-send-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.48-.084l-3.5-13.1H4.25a.75.75 0 0 1 0-1.5h5.693a.75.75 0 0 1 .12 1.49l-3.18 1.59L15 .146z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .chat-widget-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        z-index: 9999;
    }

    /* Chat Bubble Button */
    .chat-bubble-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        position: relative;
    }

    .chat-bubble-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.6);
    }

    .chat-bubble-btn:active {
        transform: scale(0.95);
    }

    .chat-bubble-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #e74c3c;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Chat Window */
    .chat-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 380px;
        max-height: 600px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 40px rgba(0, 0, 0, 0.16);
        display: flex;
        flex-direction: column;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-header-content h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .chat-subtitle {
        margin: 4px 0 0 0;
        font-size: 12px;
        opacity: 0.9;
    }

    .chat-header-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .chat-minimize-btn,
    .chat-close-btn {
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
    }

    .chat-minimize-btn:hover,
    .chat-close-btn:hover {
        background: rgba(255,255,255,0.3);
    }

    /* Messages Container */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #f5f5f5;
    }

    .chat-message {
        margin-bottom: 12px;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-message.visitor {
        display: flex;
        justify-content: flex-end;
    }

    .chat-message.agent {
        display: flex;
        justify-content: flex-start;
    }

    .chat-message.system {
        display: flex;
        justify-content: center;
    }

    .message-bubble {
        max-width: 70%;
        padding: 10px 12px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.4;
        word-wrap: break-word;
    }

    .message-bubble.visitor {
        background: #667eea;
        color: white;
    }

    .message-bubble.agent {
        background: white;
        color: #333;
        border: 1px solid #e0e0e0;
    }

    .message-bubble.system {
        background: transparent;
        color: #999;
        font-size: 12px;
        font-style: italic;
    }

    .message-bubble.ai {
        background: #e3f2fd;
        color: #0c2d79;
        border: 1px solid #bbdefb;
    }

    .message-bubble .ai-badge {
        display: inline-block;
        font-size: 10px;
        margin-right: 4px;
    }

    .message-time {
        font-size: 11px;
        margin-top: 4px;
        opacity: 0.7;
    }

    /* AI Actions Area */
    .chat-ai-actions {
        padding: 12px 16px;
        background: #f5f5f5;
        border-top: 1px solid #e0e0e0;
    }

    .chat-ai-actions .btn-outline-secondary {
        border-color: #ddd;
        color: #666;
        font-size: 12px;
    }

    .chat-ai-actions .btn-outline-secondary:hover {
        background-color: #f0f0f0;
        border-color: #bbb;
        color: #333;
    }

    /* Chat Forms */
    .chat-init-form,
    .chat-input-area {
        padding: 16px;
        background: white;
        border-top: 1px solid #e0e0e0;
    }

    .chat-init-form form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .form-control {
        border: 1px solid #ddd;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #555dd4;
    }

    .input-group {
        display: flex;
        gap: 6px;
    }

    .input-group input {
        flex: 1;
        border: 1px solid #ddd;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
    }

    .input-group input:focus {
        outline: none;
        border-color: #667eea;
    }

    .input-group .btn {
        width: auto;
        padding: 8px 12px;
    }

    /* Loading State */
    .chat-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        color: #999;
    }

    .spinner {
        width: 32px;
        height: 32px;
        border: 3px solid #f0f0f0;
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-bottom: 12px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 480px) {
        .chat-window {
            width: calc(100vw - 40px);
            max-height: calc(100vh - 120px);
        }

        .message-bubble {
            max-width: 85%;
        }
    }
</style>

<script>
console.log('Chat widget inline script loaded');

class ChatWidget {
    constructor() {
        this.chatId = null;
        this.visitorEmail = null;
        this.visitorName = null;
        this.pollInterval = null;
        this.lastMessageTime = null;
        this.aiEnabled = false;
        this.humanRequested = false;
        this.aiResponding = false;
        this.displayedMessageIds = new Set(); // Track message IDs to prevent duplicates

        console.log('ChatWidget constructor called');
        this.initializeElements();
        this.attachEventListeners();
        this.loadStoredChat();
    }

    initializeElements() {
        this.bubble = document.getElementById('chat-bubble-btn');
        this.window = document.getElementById('chat-window');
        this.minimize = document.getElementById('chat-minimize-btn');
        this.closeBtn = document.getElementById('chat-close-btn');
        this.messagesContainer = document.getElementById('chat-messages');
        this.initForm = document.getElementById('chat-init-form');
        this.inputArea = document.getElementById('chat-input-area');
        this.aiActions = document.getElementById('chat-ai-actions');
        this.startForm = document.getElementById('chat-start-form');
        this.messageForm = document.getElementById('chat-message-form');
        this.messageInput = document.getElementById('chat-message-input');
        this.talkToHumanBtn = document.getElementById('chat-talk-to-human-btn');
        this.unreadBadge = document.getElementById('chat-unread-badge');
        this.statusText = document.getElementById('chat-status');
        this.loading = document.getElementById('chat-loading');

        console.log('Chat elements initialized:', {
            bubble: !!this.bubble,
            window: !!this.window,
            minimize: !!this.minimize,
            closeBtn: !!this.closeBtn,
            aiActions: !!this.aiActions,
            loading: !!this.loading
        });
    }

    attachEventListeners() {
        if (!this.bubble) {
            console.error('Chat bubble button not found');
            return;
        }

        console.log('Attaching event listener to bubble button');
        this.bubble.addEventListener('click', () => {
            console.log('Bubble clicked!');
            this.toggleWindow();
        });

        if (this.minimize) {
            this.minimize.addEventListener('click', () => this.closeWindow());
        }

        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.handleCloseChat());
        }

        if (this.talkToHumanBtn) {
            this.talkToHumanBtn.addEventListener('click', () => this.handleTalkToHuman());
        }

        if (this.startForm) {
            this.startForm.addEventListener('submit', (e) => this.handleStartChat(e));
        }

        if (this.messageForm) {
            this.messageForm.addEventListener('submit', (e) => this.handleSendMessage(e));
        }

        console.log('Event listeners attached successfully');
    }

    toggleWindow() {
        console.log('toggleWindow called, window display:', this.window.style.display);
        if (this.window.style.display === 'none') {
            this.openWindow();
        } else {
            this.closeWindow();
        }
    }

    openWindow() {
        console.log('Opening chat window');
        this.window.style.display = 'flex';
        this.bubble.classList.add('active');
        if (this.chatId) {
            this.pollForNewMessages();
        }
    }

    closeWindow() {
        console.log('Closing chat window');
        this.window.style.display = 'none';
        this.bubble.classList.remove('active');
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    }

    loadStoredChat() {
        const stored = sessionStorage.getItem('chatWidget');
        console.log('DEBUG: loadStoredChat - raw storage value:', stored);

        if (stored) {
            try {
                const data = JSON.parse(stored);
                console.log('DEBUG: loadStoredChat - parsed data:', data);

                this.chatId = data.chatId;
                this.visitorEmail = data.visitorEmail;
                this.visitorName = data.visitorName;

                console.log('DEBUG: loadStoredChat - set values:', {
                    chatId: this.chatId,
                    visitorEmail: this.visitorEmail,
                    visitorName: this.visitorName,
                });

                this.initForm.style.display = 'none';
                this.inputArea.style.display = 'block';
                this.closeBtn.style.display = 'flex';
                this.messagesContainer.innerHTML = '';
                this.loading.style.display = 'flex';
                this.loadChatMessages();
            } catch (error) {
                console.error('ERROR: Failed to parse stored chat:', error);
                sessionStorage.removeItem('chatWidget');
            }
        } else {
            console.log('DEBUG: loadStoredChat - no stored chat found');
        }
    }

    async handleStartChat(e) {
        e.preventDefault();
        this.visitorName = document.getElementById('visitor-name').value;
        this.visitorEmail = document.getElementById('visitor-email').value;

        console.log('DEBUG: handleStartChat - form values:', {
            visitorName: this.visitorName,
            visitorEmail: this.visitorEmail,
        });

        if (!this.visitorName || !this.visitorEmail) {
            alert('Please fill in all fields');
            return;
        }

        try {
            this.loading.style.display = 'flex';
            this.messagesContainer.innerHTML = '';

            const csrfToken = this.getCsrfToken();
            console.log('Sending chat initiate request with CSRF token:', csrfToken.substring(0, 10) + '...');

            const response = await fetch('/api/chat/initiate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    visitor_name: this.visitorName,
                    visitor_email: this.visitorEmail,
                }),
            });

            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('HTTP Error Response:', errorText.substring(0, 200));
                throw new Error(`HTTP Error: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.chatId = data.chat_id;

                console.log('DEBUG: Chat created successfully', {
                    chatId: this.chatId,
                    visitorEmail: this.visitorEmail,
                    visitorName: this.visitorName,
                });

                const storageData = {
                    chatId: this.chatId,
                    visitorEmail: this.visitorEmail,
                    visitorName: this.visitorName,
                    timestamp: Date.now(),
                };

                sessionStorage.setItem('chatWidget', JSON.stringify(storageData));
                console.log('DEBUG: Stored in sessionStorage:', storageData);

                this.initForm.style.display = 'none';
                this.inputArea.style.display = 'block';
                this.closeBtn.style.display = 'flex';
                this.messagesContainer.innerHTML = '';
                this.loading.style.display = 'none';
                this.displayMessages(data.messages);
                this.updateStatus('Connected');
                this.messageInput.focus();

                // Check AI status
                await this.checkAIStatus();

                this.pollForNewMessages();
            } else {
                alert('Error starting chat: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error starting chat:', error.message);
            console.error('Full error:', error);
            alert('Failed to start chat: ' + error.message);
        }
    }

    async handleSendMessage(e) {
        e.preventDefault();
        const message = this.messageInput.value.trim();
        if (!message) return;

        this.messageInput.value = '';
        this.messageInput.focus();

        console.log('DEBUG: handleSendMessage called', {
            chatId: this.chatId,
            visitorEmail: this.visitorEmail,
            visitorEmailType: typeof this.visitorEmail,
            message: message.substring(0, 50),
            csrfToken: this.getCsrfToken().substring(0, 15),
        });

        try {
            const payload = {
                message: message,
                visitor_email: this.visitorEmail,
            };

            console.log('DEBUG: Sending payload:', payload);

            const response = await fetch(`/api/chat/${this.chatId}/message`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
                body: JSON.stringify(payload),
            });

            console.log('DEBUG: Response status:', response.status);

            const data = await response.json();
            console.log('DEBUG: Response data:', data);

            if (data.success) {
                this.addMessage(data.message.message, 'visitor', data.message.created_at, data.message.id);
                this.scrollToBottom();

                // If AI is enabled and no agent is available, get AI response
                if (this.aiEnabled && !this.humanRequested) {
                    this.getAIResponse(message);
                }
            } else {
                console.error('ERROR: Message failed with data:', data);
                alert('Error sending message: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('ERROR: Exception in handleSendMessage:', error);
            alert('Failed to send message: ' + error.message);
        }
    }

    async checkAIStatus() {
        try {
            const response = await fetch('/api/ai/status', {
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
            });

            const data = await response.json();

            if (data.success) {
                this.aiEnabled = data.ai_enabled;

                if (this.aiEnabled) {
                    this.aiActions.style.display = 'block';
                    console.log('AI is enabled for this chat');
                } else {
                    this.aiActions.style.display = 'none';
                    console.log('AI is disabled');
                }
            }
        } catch (error) {
            console.error('Error checking AI status:', error);
            this.aiActions.style.display = 'none';
        }
    }

    async getAIResponse(userMessage) {
        if (this.aiResponding) return;
        this.aiResponding = true;

        try {
            // Show typing indicator
            this.addMessage('...', 'ai', new Date().toISOString());
            const typingMessage = this.messagesContainer.lastChild;
            this.scrollToBottom();

            const response = await fetch(`/api/ai/${this.chatId}/response?visitor_email=${encodeURIComponent(this.visitorEmail)}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
                body: JSON.stringify({
                    message: userMessage,
                }),
            });

            const data = await response.json();

            if (data.success && data.message) {
                // Remove typing indicator
                typingMessage.remove();
                // Add AI response
                this.addMessage(data.message.message, 'ai', data.message.created_at, data.message.id);
                this.scrollToBottom();
            } else {
                typingMessage.remove();
                console.error('Failed to get AI response');
            }
        } catch (error) {
            console.error('Error getting AI response:', error);
            const typingMessage = this.messagesContainer.lastChild;
            if (typingMessage) typingMessage.remove();
        } finally {
            this.aiResponding = false;
        }
    }

    async handleTalkToHuman() {
        if (!this.chatId || !this.visitorEmail) {
            alert('No active chat');
            return;
        }

        try {
            const response = await fetch(`/api/ai/${this.chatId}/request-human?visitor_email=${encodeURIComponent(this.visitorEmail)}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
            });

            const data = await response.json();

            if (data.success) {
                this.humanRequested = true;
                this.aiActions.style.display = 'none';
                this.addMessage('Your request has been sent to a human agent. They will respond as soon as possible.', 'system', new Date().toISOString());
                this.updateStatus('Waiting for agent...');
                this.scrollToBottom();
            } else {
                alert('Failed to request human agent');
            }
        } catch (error) {
            console.error('Error requesting human:', error);
            alert('Failed to request human agent');
        }
    }

    async loadChatMessages() {
        try {
            const response = await fetch(`/api/chat/${this.chatId}/messages?since=${this.lastMessageTime || ''}`, {
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
            });

            const data = await response.json();

            if (data.success) {
                this.loading.style.display = 'none';
                this.displayMessages(data.messages);
                this.scrollToBottom();
                this.updateStatus('Connected');
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            this.updateStatus('Connection error');
        }
    }

    displayMessages(messages) {
        messages.forEach(msg => {
            // Skip if we've already displayed this message
            if (this.displayedMessageIds.has(msg.id)) {
                return;
            }
            this.addMessage(msg.message, msg.sender_type, msg.created_at, msg.id);
        });
    }

    addMessage(content, senderType, timestamp, messageId = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${senderType}`;

        const bubble = document.createElement('div');
        bubble.className = `message-bubble ${senderType}`;

        if (senderType === 'ai') {
            bubble.innerHTML = `<span class="ai-badge">🤖</span>${this.escapeHtml(content)}`;
        } else {
            bubble.textContent = content;
        }
        messageDiv.appendChild(bubble);

        if (senderType !== 'system') {
            const time = document.createElement('div');
            time.className = 'message-time';
            time.textContent = this.formatTime(timestamp);
            messageDiv.appendChild(time);
        }

        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
        this.lastMessageTime = timestamp;

        // Track this message ID to prevent duplicates
        if (messageId) {
            this.displayedMessageIds.add(messageId);
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async pollForNewMessages() {
        if (!this.chatId) return;

        this.pollInterval = setInterval(async () => {
            try {
                const response = await fetch(`/api/chat/${this.chatId}/messages?since=${this.lastMessageTime || ''}`, {
                    headers: {
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                const data = await response.json();

                // Check if chat has been closed by agent
                if (data.status === 'closed') {
                    this.handleChatClosed();
                    return;
                }

                if (data.success && data.messages.length > 0) {
                    this.displayMessages(data.messages);
                    this.scrollToBottom();
                    this.updateUnreadCount();
                }
            } catch (error) {
                console.error('Error polling:', error);
            }
        }, 2000);
    }

    handleChatClosed() {
        // Stop polling
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }

        // Update UI to show chat is closed
        this.updateStatus('Chat ended');

        // Hide message input and close button
        this.inputArea.style.display = 'none';
        this.closeBtn.style.display = 'none';

        // Show chat closed message
        this.addMessage('Chat has been closed by the agent. Starting a new chat will create a fresh session.', 'system', new Date().toISOString());
        this.scrollToBottom();

        // Auto-minimize widget after 5 seconds
        setTimeout(() => {
            this.closeWindow();
            this.resetChat();
        }, 5000);
    }

    resetChat() {
        // Clear all state
        this.chatId = null;
        this.visitorEmail = null;
        this.visitorName = null;
        this.aiEnabled = false;
        this.humanRequested = false;
        this.aiResponding = false;
        this.displayedMessageIds.clear(); // Clear message ID tracking

        // Clear storage
        sessionStorage.removeItem('chatWidget');

        // Reset UI
        this.messagesContainer.innerHTML = '';
        this.loading.style.display = 'none';
        this.initForm.style.display = 'block';
        this.inputArea.style.display = 'none';
        this.aiActions.style.display = 'none';
        this.closeBtn.style.display = 'none';

        // Clear form inputs
        document.getElementById('visitor-name').value = '';
        document.getElementById('visitor-email').value = '';

        // Reset status
        this.updateStatus('Connecting...');
        this.lastMessageTime = null;
    }

    async handleCloseChat() {
        if (!this.chatId || !this.visitorEmail) {
            alert('No active chat to close');
            return;
        }

        try {
            const response = await fetch(`/api/chat/${this.chatId}/close`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
                body: JSON.stringify({
                    visitor_email: this.visitorEmail,
                }),
            });

            const data = await response.json();

            if (data.success) {
                // Chat closed successfully, trigger closure handling
                this.updateStatus('Chat ended');
                this.inputArea.style.display = 'none';
                this.closeBtn.style.display = 'none';

                // Stop polling if active
                if (this.pollInterval) {
                    clearInterval(this.pollInterval);
                    this.pollInterval = null;
                }

                this.addMessage('You have closed this chat.', 'system', new Date().toISOString());
                this.scrollToBottom();

                // Auto-minimize widget after 3 seconds
                setTimeout(() => {
                    this.closeWindow();
                    this.resetChat();
                }, 3000);
            } else {
                alert('Failed to close chat: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error closing chat:', error);
            alert('Failed to close chat');
        }
    }

    updateStatus(status) {
        if (this.statusText) this.statusText.textContent = status;
    }

    updateUnreadCount() {
        const unreadMessages = this.messagesContainer.querySelectorAll('.chat-message.agent');
        if (unreadMessages.length > 0) {
            this.unreadBadge.textContent = unreadMessages.length;
            this.unreadBadge.style.display = 'flex';
        }
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        const value = token ? token.getAttribute('content') : '';
        console.log('CSRF token retrieved:', value ? '✓ Present' : '✗ Missing');
        return value;
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Initializing ChatWidget on DOMContentLoaded');
        window.chatWidget = new ChatWidget();
    });
} else {
    console.log('Initializing ChatWidget immediately');
    setTimeout(() => window.chatWidget = new ChatWidget(), 100);
}
</script>
