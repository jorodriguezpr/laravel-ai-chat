# Installation Checklist

Complete checklist for installing the Chat Widget package in any Laravel project.

## 📋 Pre-Installation

### ✅ System Requirements

- [ ] **PHP 8.1 or higher** installed and configured
- [ ] **Laravel 10.0, 11.0, or 13.0** project ready
- [ ] **Composer 2.0+** installed
- [ ] **Database** configured and accessible (MySQL, MariaDB, or PostgreSQL)
- [ ] **Web server** running (Laravel Valet, Homestead, or php artisan serve)

### ✅ Project Requirements

- [ ] **CSRF token** meta tag in layout `<head>`:
  ```blade
  <meta name="csrf-token" content="{{ csrf_token() }}">
  ```
- [ ] **Database connection** configured in `.env`
- [ ] **APP_URL** set correctly in `.env`

---

## 📦 Installation Methods

Choose **ONE** method below:

### Method 1: From Packagist (Public) ⭐ Recommended

```bash
composer require microrepairnet/chat-widget
```

**Prerequisites**: None (package must be published to Packagist first)

### Method 2: From Git Repository

**Step 1**: Add repository to `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/jorodriguezpr/laravel-ai-chat.git"
        }
    ]
}
```

**Step 2**: Install package:

```bash
composer require microrepairnet/chat-widget:^1.1
```

### Method 3: From Private Repository (Satis)

**Step 1**: Add repository to `composer.json`:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.microrepair.net"
        }
    ]
}
```

**Step 2**: Install package:

```bash
composer require microrepairnet/chat-widget
```

### Method 4: Local Development (Path)

**Step 1**: Copy package to `packages/` directory in your Laravel project

**Step 2**: Add to `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/microrepairnet/chat-widget"
        }
    ]
}
```

**Step 3**: Install with symlink:

```bash
composer require microrepairnet/chat-widget @dev
```

---

## 🗄️ Database Setup

### ✅ Run Migrations

```bash
php artisan migrate
```

**Expected Output**:
```
Migrating: 2024_12_09_000000_create_live_chats_table
Migrated:  2024_12_09_000000_create_live_chats_table (XX.XXms)
Migrating: 2024_12_09_000001_create_chat_messages_table
Migrated:  2024_12_09_000001_create_chat_messages_table (XX.XXms)
Migrating: 2024_12_10_000002_create_live_chat_settings_table
Migrated:  2024_12_10_000002_create_live_chat_settings_table (XX.XXms)
Migrating: 2024_12_10_000003_create_ai_chat_settings_table
Migrated:  2024_12_10_000003_create_ai_chat_settings_table (XX.XXms)
Migrating: 2024_12_10_000004_create_ai_provider_credentials_table
Migrated:  2024_12_10_000004_create_ai_provider_credentials_table (XX.XXms)
```

### ✅ Verify Tables Created

```bash
# Windows (PowerShell)
php artisan db:show

# Or SQL query
php artisan tinker
>>> DB::select("SHOW TABLES");
```

**Expected Tables**:
- ✅ `live_chats`
- ✅ `chat_messages`
- ✅ `live_chat_settings`
- ✅ `ai_chat_settings`
- ✅ `ai_provider_credentials`

---

## 🎨 Frontend Integration

### ✅ Add Widget to Layout

**Option A**: Include in main layout (recommended)

Edit `resources/views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ⚠️ CRITICAL: CSRF token required -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'My App')</title>
</head>
<body>
    @yield('content')
    
    <!-- Chat Widget (auto-loads on all pages) -->
    @include('chat-widget::widget')
</body>
</html>
```

**Option B**: Include on specific pages

```blade
@extends('layouts.app')

@section('content')
    <!-- Your content -->
@endsection

<!-- Add widget to this page only -->
@section('scripts')
    @include('chat-widget::widget')
@endsection
```

### ✅ Test Frontend Widget

1. **Clear caches**:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

2. **Start dev server**:
   ```bash
   php artisan serve
   ```

3. **Open browser**: http://127.0.0.1:8000

4. **Verify**:
   - [ ] Chat bubble appears in bottom-right corner
   - [ ] Click bubble opens chat window
   - [ ] Can type message and press "Send"
   - [ ] No console errors (open F12 DevTools)

---

## 🤖 AI Configuration (Optional)

### ✅ Enable AI Support

**Step 1**: Navigate to AI Settings

Visit: `http://127.0.0.1:8000/admin/ai-settings`

**Step 2**: Configure AI Provider

Choose one provider:

#### OpenAI (Recommended)

- [ ] **Provider**: Select "OpenAI"
- [ ] **API Key**: Enter your OpenAI API key
- [ ] **Model**: Select "gpt-5.4" (or gpt-5.4-mini for lower cost)
- [ ] **Enable**: Check "Enable AI responses"
- [ ] Click "Save & Test Credentials"

#### Claude (Anthropic)

- [ ] **Provider**: Select "Claude"
- [ ] **API Key**: Enter your Anthropic API key
- [ ] **Model**: Select "claude-sonnet-4-6"
- [ ] **Enable**: Check "Enable AI responses"
- [ ] Click "Save & Test Credentials"

#### Gemini (Google)

- [ ] **Provider**: Select "Gemini"
- [ ] **API Key**: Enter your Google AI API key
- [ ] **Model**: Select "gemini-2.5-flash"
- [ ] **Enable**: Check "Enable AI responses"
- [ ] Click "Save & Test Credentials"

#### GitHub Models (Free for testing)

- [ ] **Provider**: Select "GitHub Models"
- [ ] **API Key**: Enter your GitHub personal access token
- [ ] **Model**: Select "gpt-5.4"
- [ ] **Enable**: Check "Enable AI responses"
- [ ] Click "Save & Test Credentials"

**Step 3**: Verify AI Status

Visit: `http://127.0.0.1:8000/api/ai/status`

**Expected Response**:
```json
{
  "enabled": true,
  "provider": "openai",
  "model": "gpt-5.4"
}
```

### ✅ Test AI Responses

1. Open chat widget on frontend
2. Send message: "Hello, are you there?"
3. Wait for AI response (should appear within 3-5 seconds)
4. Verify response is relevant and natural

---

## 👥 Admin Panel Setup

### ✅ Publish Admin Assets (Optional)

Only needed if you want to customize admin views:

```bash
# Publish admin views
php artisan vendor:publish --tag="chat-widget-views"

# Publish controllers (for customization)
php artisan vendor:publish --tag="chat-widget-controllers"

# Publish routes (to modify endpoints)
php artisan vendor:publish --tag="chat-widget-routes"
```

### ✅ Access Admin Panel

**Option A**: Using Package Routes (Default)

Visit directly: `http://127.0.0.1:8000/admin/live-chat`

No additional configuration needed!

**Option B**: Custom Admin Middleware

If you want to add authentication middleware:

1. Publish routes:
   ```bash
   php artisan vendor:publish --tag="chat-widget-routes"
   ```

2. Edit `routes/chat-widget.php`:
   ```php
   Route::middleware(['web', 'auth', 'admin'])->prefix('admin')->group(function () {
       // Routes already included
   });
   ```

### ✅ Test Admin Features

1. **View Active Chats**: http://127.0.0.1:8000/admin/live-chat
   - [ ] See list of chats with status (pending/active/closed)
   - [ ] Tabs work (Pending, Active, Closed)

2. **Pickup Pending Chat**:
   - [ ] Create new chat from frontend (visitor side)
   - [ ] Admin sees chat in "Pending" tab
   - [ ] Click "Pickup Chat" button
   - [ ] Chat moves to "Active" tab

3. **Send Agent Message**:
   - [ ] Open active chat
   - [ ] Type agent message
   - [ ] Click "Send"
   - [ ] Message appears in visitor's chat (real-time polling)

4. **Close Chat**:
   - [ ] Click "Close Chat" button
   - [ ] Chat moves to "Closed" tab
   - [ ] Visitor sees "Chat ended" message

---

## ⚙️ Configuration (Optional)

### ✅ Publish Configuration File

```bash
php artisan vendor:publish --tag="chat-widget-config"
```

Edit `config/chat-widget.php`:

```php
<?php

return [
    'enabled' => env('CHAT_WIDGET_ENABLED', true),
    
    'widget' => [
        'position' => env('CHAT_WIDGET_POSITION', 'bottom-right'), // bottom-right, bottom-left, top-right, top-left
        'theme' => env('CHAT_WIDGET_THEME', 'light'), // light, dark
        'title' => env('CHAT_WIDGET_TITLE', 'Chat with us'),
        'subtitle' => env('CHAT_WIDGET_SUBTITLE', 'We typically reply in minutes'),
        'auto_close_after_hours' => 24,
    ],
    
    'ai' => [
        'enabled' => env('AI_CHAT_ENABLED', false),
        'default_provider' => env('AI_CHAT_PROVIDER', 'openai'),
        'timeout' => 30,
    ],
];
```

### ✅ Environment Variables

Add to `.env`:

```env
# Chat Widget
CHAT_WIDGET_ENABLED=true
CHAT_WIDGET_POSITION=bottom-right
CHAT_WIDGET_THEME=light
CHAT_WIDGET_TITLE="Chat with us"
CHAT_WIDGET_SUBTITLE="We typically reply in minutes"

# AI Settings
AI_CHAT_ENABLED=true
AI_CHAT_PROVIDER=openai
```

### ✅ Clear Configuration Cache

After changing config:

```bash
php artisan config:cache
```

---

## 🔒 Security Checklist

### ✅ Production Security

- [ ] **HTTPS enforced** (SSL certificate installed)
- [ ] **APP_DEBUG=false** in production `.env`
- [ ] **CSRF protection** verified (meta tag present)
- [ ] **Database credentials** not in version control
- [ ] **AI API keys** stored in database (encrypted) or `.env`
- [ ] **Rate limiting** configured (optional):
  ```php
  Route::middleware(['throttle:60,1'])->group(function () {
      // Chat routes
  });
  ```

### ✅ File Permissions

```bash
# Windows - generally not needed
# Linux/Mac:
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🧪 Testing Checklist

### ✅ Visitor Flow Test

1. **Open frontend** as guest/visitor
2. **Click chat bubble**
3. **Send message**: "Hello, I need help"
4. **Verify**:
   - [ ] Message sent successfully
   - [ ] Timestamp appears
   - [ ] AI response received (if enabled)
   - [ ] No errors in console

### ✅ Agent Flow Test

1. **Open admin panel**: http://127.0.0.1:8000/admin/live-chat
2. **See pending chat** in "Pending" tab
3. **Click chat** to open details
4. **Click "Pickup Chat"**
5. **Send agent message**: "Hi, how can I help?"
6. **Verify**:
   - [ ] Chat status changed to "Active"
   - [ ] Agent message appears in visitor's chat (within 3 seconds)
   - [ ] Visitor can reply
   - [ ] Messages sync bidirectionally

### ✅ AI Test (If Enabled)

1. **Send visitor message** without agent pickup
2. **Verify**:
   - [ ] AI responds within 5 seconds
   - [ ] Response is relevant
   - [ ] No error messages

### ✅ Close Chat Test

1. **Agent clicks "Close Chat"**
2. **Verify**:
   - [ ] Chat moves to "Closed" tab
   - [ ] Visitor sees "Chat ended" or input disabled
   - [ ] Can start new chat

---

## 🐛 Troubleshooting

### Issue: "View [chat-widget::widget] not found"

**Solution**:
```bash
composer update microrepairnet/chat-widget
php artisan cache:clear
php artisan view:clear
```

### Issue: CSRF Token Errors

**Solution**: Add to layout `<head>`:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Issue: 404 on API Routes

**Solution**:
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list | grep chat
```

### Issue: AI Not Responding (403 Error)

**Solution**:
1. Check API key in `/admin/ai-settings`
2. Click "Test Credentials"
3. Verify logs: `storage/logs/laravel.log`

### Issue: Messages Not Polling

**Solution**:
```bash
php artisan cache:clear
```

Check browser console for endpoint errors.

---

## 📊 Verification Commands

### ✅ Package Installed

```bash
composer show microrepairnet/chat-widget
```

**Expected Output**:
```
name     : microrepairnet/chat-widget
versions : * 1.1.0
type     : library
...
```

### ✅ Routes Registered

```bash
php artisan route:list | grep chat
```

**Expected Routes**:
- `/api/chat/initiate`
- `/api/chat/{chat}/message`
- `/api/chat/{chat}/messages`
- `/admin/live-chat`
- `/admin/live-chat/{chat}`
- `/admin/ai-settings`

### ✅ Migrations Run

```bash
php artisan migrate:status
```

**Expected**:
```
Ran   2024_12_09_000000_create_live_chats_table
Ran   2024_12_09_000001_create_chat_messages_table
Ran   2024_12_10_000002_create_live_chat_settings_table
Ran   2024_12_10_000003_create_ai_chat_settings_table
Ran   2024_12_10_000004_create_ai_provider_credentials_table
```

### ✅ Views Available

```bash
php artisan view:list | grep chat-widget
```

### ✅ Service Provider Loaded

```bash
php artisan package:discover
```

**Should list**: `Microrepairnet\ChatWidget\ChatWidgetServiceProvider`

---

## ✅ Final Verification

After completing all steps above:

- [ ] **Widget visible** on frontend
- [ ] **Can send messages** as visitor
- [ ] **Admin panel accessible**
- [ ] **Can pickup chats** as agent
- [ ] **Messages sync** bidirectionally
- [ ] **AI responds** (if enabled)
- [ ] **Can close chats**
- [ ] **No errors in logs**: `tail -f storage/logs/laravel.log`
- [ ] **No console errors**: Browser DevTools (F12)

---

## 📚 Next Steps

Once installation is verified:

1. **Read Documentation**:
   - [README.md](README.md) - Overview and features
   - [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md) - Code examples
   - [DEPLOYMENT.md](DEPLOYMENT.md) - Production deployment guide

2. **Customize**:
   - Publish views: `php artisan vendor:publish --tag=chat-widget-views`
   - Edit theme, colors, positioning

3. **Integrate**:
   - Add authentication middleware to admin routes
   - Customize AI prompts
   - Export chat history

4. **Deploy**:
   - Follow [DEPLOYMENT.md](DEPLOYMENT.md) for production setup
   - Configure monitoring and backups

---

## 🆘 Support

**Issues**: https://github.com/jorodriguezpr/laravel-ai-chat/issues  
**Email**: jrpcone@gmail.com  
**Documentation**: [README.md](README.md)

---

**Package Version**: 1.1.0  
**Last Updated**: April 15, 2026  
**Author**: Jose Rodriguez Arroyo
