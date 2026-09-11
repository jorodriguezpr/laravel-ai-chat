# Installation Instructions

## ⚠️ CRITICAL REQUIREMENT

Your Laravel layout **must** include the CSRF token meta tag in the `<head>` section:

```blade
<html>
<head>
    <!-- REQUIRED for chat widget to function -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- ... rest of head ... -->
</head>
<body>
    @yield('content')
    <!-- Include chat widget in body or layout -->
    @include('chat-widget::widget')
</body>
</html>
```

**Without this CSRF token, the chat widget will not be able to send messages and you'll see errors in the browser console.**

## Quick Start (Minimum Steps Required)

### For Windows Users (PowerShell)

```powershell
# 1. Add repository to composer.json
$composer = Get-Content composer.json | ConvertFrom-Json
$composer.repositories += @{
    type = "path"
    url = "./packages/microrepairnet/chat-widget"
}
$composer | ConvertTo-Json -Depth 10 | Set-Content composer.json

# 2. Install package
composer require microrepairnet/chat-widget

# 3. Run migrations (required)
php artisan migrate

# 4. Clear cache
php artisan cache:clear
```

### For Linux/Mac Users (Bash)

```bash
# Run the included install script
bash INSTALL.sh
```

## What Gets Installed Automatically ✅

After running `composer require microrepairnet/chat-widget`, these are **automatically loaded** by the service provider:

- ✅ **Routes** - API endpoints available at `/api/chat/*` and `/api/ai/*` (auto-registered)
- ✅ **Views** - Blade templates available via `chat-widget::` namespace (auto-registered)
- ✅ **Migrations** - Database schemas auto-discovered (ready to run)
- ✅ **Models** - Available at `Microrepairnet\ChatWidget\Models\*` (auto-loaded)
- ✅ **Controllers** - Available via package namespace (auto-discoverable)
- ✅ **Config** - Merged with default `config/chat-widget.php` (auto-registered)
- ✅ **Services** - AI Chat Service registered as singleton (auto-instantiated)

**✨ No additional configuration needed for these items to function!**

## Required Manual Steps

### Step 1: Run Migrations

```bash
php artisan migrate
```

This creates the required database tables:
- `live_chats` - Chat session records
- `chat_messages` - Message history
- `live_chat_settings` - Chat widget settings
- `ai_chat_settings` - AI provider configuration
- `ai_provider_credentials` - API key storage

### Step 2: (Optional) Publish Assets for Customization

The package supports publishing **all** assets for full customization. Choose what you need:

#### Option A: Publish Everything (Recommended for full control)

```bash
php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget"
```

This publishes:
- ✅ Controllers → `app/Http/Controllers/ChatWidget/`
- ✅ Views → `resources/views/vendor/chat-widget/`
- ✅ Routes → `routes/chat-widget.php`
- ✅ Models → `app/Models/ChatWidget/`
- ✅ Config → `config/chat-widget.php`

#### Option B: Publish Specific Assets Only

```bash
# Configuration file only
php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-config"

# Views only (for template customization)
php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-views"

# Controllers only (for logic customization)
php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-controllers"

# Routes only (for endpoint customization)
php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-routes"

# Models only (for schema customization)
php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-models"

# Migrations only
php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-migrations"
```

After publishing, you can customize:
- `config/chat-widget.php` - Widget settings
- `resources/views/vendor/chat-widget/*` - Template customization
- `app/Http/Controllers/ChatWidget/*` - Controller logic
- `routes/chat-widget.php` - API endpoints
- `app/Models/ChatWidget/*` - Database models

### Step 3: (Optional) Add Admin Routes

If you want to add an admin panel for managing live chats and AI settings:

#### Option 1: Use Published Routes (After Step 2)

After publishing routes with `--tag="chat-widget-routes"`, register them in `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

// Include published chat-widget routes
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    // Load all chat-widget routes
    include base_path('routes/chat-widget.php');
});
```

#### Option 2: Use Package Controllers Directly

If you prefer not to publish routes, use package controllers directly in `routes/web.php`:

```php
use Illuminate\Support\Facades\Route;
use Microrepairnet\ChatWidget\Controllers\LiveChatAgentController;
use Microrepairnet\ChatWidget\Controllers\AISettingsController;

// Admin Panel Routes (Protected by AdminMiddleware)
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    // ... existing admin routes ...

    // Live Chat Management Routes
    Route::prefix('live-chat')->name('live-chat.')->group(function () {
        Route::get('/', [LiveChatAgentController::class, 'index'])->name('index');
        Route::get('/{chat}', [LiveChatAgentController::class, 'show'])->name('show');
        Route::post('/{chat}/message', [LiveChatAgentController::class, 'sendMessage'])->name('send-message');
        Route::post('/{chat}/close', [LiveChatAgentController::class, 'close'])->name('close');
        Route::get('/api/chats', [LiveChatAgentController::class, 'chats'])->name('chats');
        Route::get('/api/{chat}/messages', [LiveChatAgentController::class, 'getMessages'])->name('get-messages');
    });

    // AI Settings Routes
    Route::prefix('ai-settings')->name('ai-settings.')->group(function () {
        Route::get('/', [AISettingsController::class, 'index'])->name('index');
        Route::post('/update', [AISettingsController::class, 'updateSettings'])->name('update');
        Route::post('/save-credentials', [AISettingsController::class, 'saveCredentials'])->name('save-credentials');
        Route::post('/delete-credentials', [AISettingsController::class, 'deleteCredentials'])->name('delete-credentials');
        Route::get('/models', [AISettingsController::class, 'getProviderModels'])->name('models');
    });
});
```

### Step 4: (Optional) Configure Environment

Add to your `.env` file:

```env
# Chat Widget Configuration
CHAT_WIDGET_ENABLED=true
CHAT_WIDGET_POSITION=bottom-right
CHAT_WIDGET_THEME=light
CHAT_WIDGET_TITLE="Chat with us"
CHAT_WIDGET_SUBTITLE="We typically reply in minutes"

# AI Configuration (optional)
AI_CHAT_ENABLED=true
AI_CHAT_PROVIDER=openai
# Add your API keys to the admin panel at /admin/ai-settings
```

### Step 5: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Verification

After completing the quick start, verify the installation:

1. ✅ Check migrations ran: 
   ```php
   php artisan tinker
   \DB::table('live_chats')->count()
   ```

2. ✅ Verify routes are loaded:
   ```bash
   php artisan route:list | grep chat
   ```

3. ✅ Test frontend widget (if added to views)

4. ✅ Visit admin dashboard (if routes added): `/admin/live-chat`

5. ✅ Configure AI settings (if routes added): `/admin/ai-settings`

## Package Structure

Package files are located in `packages/microrepairnet/chat-widget/`:

```
├── config/
│   └── chat-widget.php                    ✅ Auto-merged | 📦 Publishable
├── database/
│   └── migrations/                        ✅ Auto-loaded | 📦 Publishable
├── resources/
│   └── views/                             ✅ Auto-loaded | 📦 Publishable
├── routes/
│   └── web.php                            ✅ Auto-loaded | 📦 Publishable
├── src/
│   ├── Models/                            ✅ Auto-available | 📦 Publishable
│   ├── Controllers/                       ✅ Auto-available | 📦 Publishable
│   ├── Services/
│   │   └── AI/                            ✅ Auto-registered
│   ├── Providers/
│   │   └── ChatWidgetServiceProvider.php  ✅ Auto-discovered
│   └── Middleware/
├── README.md
├── composer.json
└── INSTALLATION.md
```

**Legend:**
- `✅ Auto-loaded` = Works immediately after `composer require` (no manual action needed)
- `📦 Publishable` = Can be copied to your app for customization with `php artisan vendor:publish`

## Troubleshooting

### Package not found

1. Ensure `packages/microrepairnet/chat-widget/` directory exists
2. Run `composer update` to discover the package
3. Verify `composer.json` has the path repository

### Migrations not running

1. Check `php artisan migrate:status`
2. Ensure migrations are published: `php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-migrations"`
3. Check database credentials in `.env`

### Routes not working

1. Clear route cache: `php artisan route:clear`
2. Verify routes are loaded: `php artisan route:list | grep chat`

### Models not found

1. Ensure autoloading is set up in `composer.json`
2. Run `composer dump-autoload`
3. Clear cache: `php artisan cache:clear`

## Next Steps

1. **Add widget to your frontend** - Include in your layout:
   ```blade
   @include('chat-widget::widget')
   ```

2. **Customize the widget** (Optional) - Publish views and edit templates:
   ```bash
   php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-views"
   ```

3. **Set up admin panel** (Optional):
   - Publish controllers and routes:
   ```bash
   php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-controllers"
   php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-routes"
   ```
   - Register admin routes in `routes/web.php` (see Step 3 above)
   - Visit `/admin/live-chat` to manage conversations

4. **Configure AI providers** (Optional):
   - Visit admin dashboard: `/admin/ai-settings`
   - Add API credentials for desired providers
   - Configure default AI model and settings

5. **Customize settings** - Edit `config/chat-widget.php`:
   ```bash
   php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-config"
   ```

6. **Use package classes** - Import in your application:
   ```php
   use Microrepairnet\ChatWidget\Models\LiveChat;
   use Microrepairnet\ChatWidget\Models\ChatMessage;
   use Microrepairnet\ChatWidget\Models\AIChatSetting;
   use Microrepairnet\ChatWidget\Services\AIChatService;
   ```
