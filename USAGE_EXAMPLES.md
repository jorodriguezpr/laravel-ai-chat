# Chat Widget Package - Usage Examples

## Basic Widget Integration

### 1. Include Widget on Frontend

```blade
<!-- In your layout or template -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Your page content -->
    </div>

    <!-- Chat widget will appear in bottom-right -->
    @include('chat-widget::widget')
@endsection
```

## Using Models

### Get Active Chats

```php
use Microrepairnet\ChatWidget\Models\LiveChat;

// Get all active chats
$activeChats = LiveChat::where('status', 'active')->get();

// Get chats with messages
$chats = LiveChat::with('messages')->get();

// Get specific chat
$chat = LiveChat::find($id);
```

### Add Messages Programmatically

```php
use Microrepairnet\ChatWidget\Models\LiveChat;
use Microrepairnet\ChatWidget\Models\ChatMessage;

$chat = LiveChat::find($id);

// Add visitor message
$chat->addMessage('Hello!', ChatMessage::SENDER_VISITOR);

// Add AI response
$chat->addMessage('Hi there!', ChatMessage::SENDER_AI);

// Add system message
$chat->addMessage('Agent joined the chat', ChatMessage::SENDER_SYSTEM);
```

## Using AI Service

### Check AI Status

```php
use Microrepairnet\ChatWidget\Services\AI\AIChatService;

$aiService = app(AIChatService::class);

if ($aiService->isEnabled()) {
    echo "AI is enabled and ready!";
}
```

### Send Message to AI

```php
use Microrepairnet\ChatWidget\Services\AI\AIChatService;
use Microrepairnet\ChatWidget\Models\LiveChat;

$chat = LiveChat::find($id);
$aiService = app(AIChatService::class);

// Get conversation history
$history = $chat->messages()
    ->whereIn('sender_type', ['visitor', 'ai'])
    ->get(['sender_type', 'message'])
    ->toArray();

// Send message to AI
$response = $aiService->sendMessage(
    "User message here",
    $history
);

// Save AI response
$chat->addMessage($response, 'ai');
```

### Get Available Providers

```php
use Microrepairnet\ChatWidget\Services\AI\AIChatService;

$providers = AIChatService::getAvailableProviders();
// Returns: ['openai' => 'OpenAI (ChatGPT)', 'claude' => 'Claude (Anthropic)', ...]
```

## Query Examples

### Get chats with unread messages

```php
use Microrepairnet\ChatWidget\Models\LiveChat;
use Microrepairnet\ChatWidget\Models\ChatMessage;

$chatsWithUnread = LiveChat::whereHas('messages', function ($query) {
    $query->where('is_read', false)
          ->where('sender_type', '!=', 'visitor');
})->get();
```

### Get recent chats

```php
use Microrepairnet\ChatWidget\Models\LiveChat;

$recentChats = LiveChat::latest('updated_at')
    ->limit(10)
    ->get();
```

### Get messages from specific date

```php
use Microrepairnet\ChatWidget\Models\ChatMessage;

$todayMessages = ChatMessage::where('sender_type', '!=', 'system')
    ->whereDate('created_at', today())
    ->get();
```

### Get AI conversations only

```php
use Microrepairnet\ChatWidget\Models\LiveChat;
use Microrepairnet\ChatWidget\Models\ChatMessage;

$aiChats = LiveChat::whereHas('messages', function ($query) {
    $query->where('sender_type', 'ai');
})->get();

// Count AI responses
$aiResponseCount = ChatMessage::where('sender_type', 'ai')->count();
```

## Controller Examples

### Inject AI Service in Controller

```php
<?php

namespace App\Http\Controllers;

use Microrepairnet\ChatWidget\Services\AI\AIChatService;
use Microrepairnet\ChatWidget\Models\LiveChat;

class ChatController extends Controller
{
    public function __construct(private AIChatService $aiService)
    {}

    public function handleMessage(LiveChat $chat, Request $request)
    {
        $message = $request->input('message');

        // Save visitor message
        $chat->addMessage($message, 'visitor');

        // Get AI response if enabled
        if ($this->aiService->isEnabled()) {
            try {
                // Get conversation history
                $history = $chat->messages()
                    ->limit(10)
                    ->get(['sender_type', 'message'])
                    ->toArray();

                $response = $this->aiService->sendMessage($message, $history);
                $chat->addMessage($response, 'ai');

                return response()->json([
                    'success' => true,
                    'ai_response' => $response
                ]);
            } catch (\Exception $e) {
                \Log::error('AI error: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true]);
    }
}
```

## Configuration Examples

### Custom Widget Position (in .env)

```env
CHAT_WIDGET_POSITION=bottom-left      # bottom-right, bottom-left, top-right, top-left
CHAT_WIDGET_THEME=dark                # light, dark
CHAT_WIDGET_TITLE="Support"           # Custom title
CHAT_WIDGET_SUBTITLE="Ask us anything"  # Custom subtitle
```

### Set AI System Prompt (Programmatically)

```php
use Microrepairnet\ChatWidget\Models\AIChatSetting;

$settings = AIChatSetting::current();
$settings->update([
    'system_prompt' => 'You are a friendly support agent for an ecommerce store. Help customers with their orders and questions.'
]);
```

### Multi-Provider Setup

```php
use Microrepairnet\ChatWidget\Models\AIProviderCredential;

// Add OpenAI
AIProviderCredential::create([
    'provider' => 'openai',
    'api_key' => env('OPENAI_API_KEY'),
    'model' => 'gpt-4',
    'is_active' => true
]);

// Add Claude
AIProviderCredential::create([
    'provider' => 'claude',
    'api_key' => env('CLAUDE_API_KEY'),
    'model' => 'claude-3-opus-20240229',
    'is_active' => false  // Not active yet
]);
```

## Event/Listener Pattern

### Example: Log all chat activity

```php
<?php

namespace App\Listeners;

use Microrepairnet\ChatWidget\Models\ChatMessage;

class LogChatActivity
{
    public function handle(MessageCreated $event)
    {
        \Log::info('Chat message', [
            'chat_id' => $event->message->live_chat_id,
            'sender' => $event->message->sender_type,
            'message' => substr($event->message->message, 0, 100),
        ]);
    }
}
```

## Reporting Example

### Generate chat statistics

```php
use Microrepairnet\ChatWidget\Models\LiveChat;
use Microrepairnet\ChatWidget\Models\ChatMessage;

// Total conversations
$totalChats = LiveChat::count();

// AI response count
$aiResponses = ChatMessage::where('sender_type', 'ai')->count();

// Average chat duration (in minutes)
$avgDuration = LiveChat::where('status', 'closed')
    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_duration')
    ->pluck('avg_duration')
    ->first();

// Message count by type
$messageStats = ChatMessage::selectRaw('sender_type, COUNT(*) as count')
    ->groupBy('sender_type')
    ->get();

return [
    'total_chats' => $totalChats,
    'ai_responses' => $aiResponses,
    'avg_duration_minutes' => $avgDuration,
    'message_breakdown' => $messageStats,
];
```

## Advanced: Custom Provider

### Example: Add support for custom AI provider

```php
<?php

namespace App\Services\AI;

use Microrepairnet\ChatWidget\Services\AI\AIProviderInterface;

class CustomAIProvider implements AIProviderInterface
{
    public function __construct(string $apiKey, string $model = 'default')
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function sendMessage(string $message, string $systemPrompt, array $conversationHistory = []): string
    {
        // Your custom implementation
        return "Response from custom provider";
    }

    public function validateCredentials(): bool
    {
        // Validate API key
        return true;
    }

    public function getAvailableModels(): array
    {
        return [
            'model1' => 'Model 1',
            'model2' => 'Model 2',
        ];
    }
}
```

## Middleware Example

### Protect admin routes

```php
<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
```

## Testing Example

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Microrepairnet\ChatWidget\Models\LiveChat;
use Microrepairnet\ChatWidget\Models\ChatMessage;

class ChatWidgetTest extends TestCase
{
    public function test_can_create_chat()
    {
        $chat = LiveChat::create([
            'visitor_name' => 'John Doe',
            'visitor_email' => 'john@example.com',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('live_chats', [
            'visitor_email' => 'john@example.com',
        ]);
    }

    public function test_can_add_message()
    {
        $chat = LiveChat::factory()->create();
        $message = $chat->addMessage('Hello', ChatMessage::SENDER_VISITOR);

        $this->assertDatabaseHas('chat_messages', [
            'message' => 'Hello',
            'sender_type' => 'visitor',
        ]);
    }

    public function test_ai_service_enabled()
    {
        $aiService = app('AIChatService');
        $this->assertFalse($aiService->isEnabled());  // Unless configured
    }
}
```

---

For more examples and use cases, see the main README.md in the package root.
