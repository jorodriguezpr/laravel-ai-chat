# Quick Start Guide - Chat Widget Package

## ⚡ Installation (3 minutes)

### Prerequisites

Your layout must include the CSRF token meta tag in the `<head>` section:

```blade
<head>
    <!-- ... other meta tags ... -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
```

### For New Projects

```bash
# 1. Add package to composer.json
composer require microrepairnet/chat-widget

# 2. Run migrations
php artisan migrate

# 3. Add widget to your layout (resources/views/layouts/app.blade.php)
@include('chat-widget::widget')

# 4. Ensure your layout has CSRF token in <head>
<meta name="csrf-token" content="{{ csrf_token() }}">

# Done! ✅
```

## 📋 What's Auto-Installed

After `composer require`, you get:

| Component | Location | Auto-Loaded? |
|-----------|----------|--------------|
| Routes | `/api/chat/*`, `/api/ai/*` | ✅ Yes |
| Views | `chat-widget::` namespace | ✅ Yes |
| Models | `Microrepairnet\ChatWidget\Models\*` | ✅ Yes |
| Controllers | `Microrepairnet\ChatWidget\Controllers\*` | ✅ Yes |
| Config | `config/chat-widget.php` | ✅ Yes (merged) |
| Services | `Microrepairnet\ChatWidget\Services\*` | ✅ Yes |

**✨ No configuration needed for basic functionality!**

## 🎨 Customization Options

### Option 1: Use Package Components (Default)

Use everything from the package as-is:
- Views loaded from package
- Controllers auto-registered
- Routes work out of the box

### Option 2: Publish & Customize

Publish specific components to your app:

```bash
# Publish everything
php artisan vendor:publish --tag="chat-widget"

# Or publish specific components
php artisan vendor:publish --tag="chat-widget-views"       # Edit templates
php artisan vendor:publish --tag="chat-widget-controllers" # Customize logic
php artisan vendor:publish --tag="chat-widget-routes"      # Modify endpoints
php artisan vendor:publish --tag="chat-widget-config"      # Update settings
```

## 🎯 Next Steps

### Step 1: Add Widget to Your Views

```blade
<!-- In resources/views/layouts/app.blade.php or any template -->
@include('chat-widget::widget')
```

### Step 2: Set Up Admin Panel (Optional)

If you want to manage conversations and configure AI:

```bash
# Publish controllers and routes
php artisan vendor:publish --tag="chat-widget-controllers"
php artisan vendor:publish --tag="chat-widget-routes"
```

Add to `routes/web.php`:

```php
Route::middleware('admin')->prefix('admin')->group(function () {
    include base_path('routes/chat-widget.php');
});
```

Visit `/admin/live-chat` to manage conversations.

### Step 3: Configure AI (Optional)

1. Visit `/admin/ai-settings`
2. Add API credentials for your preferred provider:
   - OpenAI
   - Claude (Anthropic)
   - Google Gemini
   - GitHub Models
3. Select default model
4. Save settings

## 📁 File Locations After Publishing

```
project-root/
├── app/
│   ├── Http/Controllers/
│   │   └── ChatWidget/           (if published)
│   └── Models/
│       └── ChatWidget/           (if published)
├── config/
│   └── chat-widget.php           (if published)
├── resources/views/vendor/
│   └── chat-widget/              (if published)
├── routes/
│   └── chat-widget.php           (if published)
└── database/migrations/
    └── *_create_chat_*_table.php (auto-migrated)
```

## 🔧 Common Tasks

### Change Widget Position

Edit `config/chat-widget.php`:

```php
'widget' => [
    'position' => 'bottom-right', // or 'bottom-left', 'top-right', 'top-left'
    'theme' => 'light',           // or 'dark'
    'title' => 'Chat with us',
    'subtitle' => 'We typically reply in minutes',
],
```

### Enable AI Responses

1. Go to `/admin/ai-settings`
2. Enable AI toggle
3. Add API credentials
4. Select default provider and model
5. Save

### Customize Widget Template

```bash
# Publish views
php artisan vendor:publish --tag="chat-widget-views"

# Edit template
# resources/views/vendor/chat-widget/widget.blade.php
```

### Modify API Endpoints

```bash
# Publish routes
php artisan vendor:publish --tag="chat-widget-routes"

# Edit endpoints
# routes/chat-widget.php
```

## 🐛 Troubleshooting

### Widget not showing?

1. Check migration ran: `php artisan migrate:status`
2. Clear cache: `php artisan cache:clear`
3. Verify include in template: `@include('chat-widget::widget')`
4. **CRITICAL**: Ensure CSRF token is in your layout `<head>`:
   ```blade
   <meta name="csrf-token" content="{{ csrf_token() }}">
   ```

### "View [widget] not found" Error?

✅ **FIXED in latest version** - Update your package:

```bash
composer update microrepairnet/chat-widget
php artisan cache:clear
```

### Routes not working?

```bash
php artisan route:clear
php artisan route:list | grep api/chat
```

### Chat send fails silently?

1. Check browser console for errors (F12)
2. Verify CSRF token in page source: `<meta name="csrf-token">`
3. Check Laravel error logs: `storage/logs/laravel.log`
4. Verify migrations ran: `php artisan migrate:status`

### Controllers not found?

```bash
# Publish controllers
php artisan vendor:publish --tag="chat-widget-controllers"

# Clear autoload
composer dump-autoload
```

## 📚 Documentation

- **Full Installation Guide**: [INSTALLATION.md](INSTALLATION.md)
- **AI Models Info**: [AI_MODELS_UPDATE.md](AI_MODELS_UPDATE.md)
- **Usage Examples**: [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md)
- **Package Setup**: [PACKAGE_SETUP.md](PACKAGE_SETUP.md)

## 💬 Support

- GitHub: https://github.com/jorodriguezpr/laravel-ai-chat
- Issues: https://github.com/jorodriguezpr/laravel-ai-chat/issues
