# Troubleshooting Guide - Chat Widget Package

## Common Issues & Solutions

### 1. ❌ "View [widget] not found" OR "InvalidArgumentException"

**Error Message:**
```
InvalidArgumentException
vendor\laravel\framework\src\Illuminate\View\FileViewFinder.php:138
View [widget] not found.
```

**Cause:** The widget view file is missing or the package wasn't properly installed.

**Solution:**

#### Option A: Update Package (Recommended)
```bash
composer update microrepairnet/chat-widget
php artisan cache:clear
php artisan view:clear
```

#### Option B: Clear Everything
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Reinstall package
composer require microrepairnet/chat-widget --force
php artisan migrate
```

#### Option C: Manual Fix
If updating doesn't work, manually create the file:

1. Create: `resources/views/vendor/chat-widget/widget.blade.php`
2. Add this content:
```blade
@include('chat-widget::components.chat-widget')
```

---

### 2. ❌ "Meta tag [csrf-token] not found" or Messages Won't Send

**Error in Browser Console:**
```
CSRF token retrieved: ✗ Missing
```

**Cause:** Your layout is missing the CSRF token meta tag.

**Solution:**

Add this to your layout's `<head>` section:

```blade
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
```

**Example (resources/views/layouts/app.blade.php):**
```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My App</title>
</head>
<body>
    @yield('content')
    
    <!-- Chat Widget -->
    @include('chat-widget::widget')
</body>
</html>
```

---

### 3. ❌ Widget Shows But Chat Doesn't Work

**Symptoms:**
- Chat bubble appears but can't send messages
- Form submits but nothing happens
- Browser console shows errors

**Diagnosis:**

1. **Check CSRF Token:**
   Open browser console (F12) and run:
   ```javascript
   document.querySelector('meta[name="csrf-token"]').getAttribute('content')
   ```
   Should return something like: `9x8c7v6b5n4m3l2k1j0i` (not empty)

2. **Check Migrations:**
   ```bash
   php artisan migrate:status
   ```
   All chat-widget migrations should show "Ran".

3. **Check Routes:**
   ```bash
   php artisan route:list | grep api/chat
   ```
   Should show routes like:
   - `POST /api/chat/initiate`
   - `POST /api/chat/{chat}/message`
   - `GET /api/chat/{chat}/messages`

4. **Check Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   # or on Windows:
   Get-Content storage\logs\laravel.log | Select-Object -Last 50
   ```

---

### 4. ❌ Package Not Found After Composer Require

**Error:**
```
Could not find package microrepairnet/chat-widget
```

**Cause:** Composer doesn't know where the package is located.

**Solution:**

1. **Add Path Repository** to `composer.json`:
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

2. **Update Composer:**
   ```bash
   composer update
   composer require microrepairnet/chat-widget
   ```

3. **Verify Directory Structure:**
   ```
   project-root/
   ├── packages/
   │   └── microrepairnet/
   │       └── chat-widget/     ← Must exist here
   ├── composer.json
   └── ...
   ```

---

### 5. ❌ Migrations Fail to Run

**Error:**
```
Migration not found
```

**Solution:**

```bash
# Publish migrations first
php artisan vendor:publish --tag="chat-widget-migrations"

# Then run them
php artisan migrate
```

**Check migration status:**
```bash
php artisan migrate:status
```

---

### 6. ❌ "Class not found" for Models or Controllers

**Error:**
```
Class 'Microrepairnet\ChatWidget\Models\LiveChat' not found
```

**Solution:**

```bash
# Regenerate autoloader
composer dump-autoload

# Clear Laravel cache
php artisan cache:clear
php artisan config:clear

# Clear compiled files
php artisan optimize:clear
```

---

### 7. ❌ Widget Not Appearing on Page

**Cause:** Widget view not included in template.

**Solution:**

Make sure your layout includes:
```blade
@include('chat-widget::widget')
```

**Common mistakes to avoid:**
- ❌ `@include('chat-widget')` - Missing subdirectory
- ❌ `@include('widget')` - Missing namespace
- ❌ `@include('chat-widget::chat-widget')` - Wrong filename
- ✅ `@include('chat-widget::widget')` - Correct!

---

### 8. ❌ Routes Not Loading

**Check if routes are registered:**
```bash
php artisan route:list | grep api/chat
```

**If no routes appear:**

1. Clear route cache:
   ```bash
   php artisan route:clear
   ```

2. Verify service provider is loaded:
   ```bash
   php artisan tinker
   > app('Illuminate\Routing\Router')->getRoutes()->getByName('chat.initiate')
   ```

3. Check if provider is in `config/app.php`:
   ```php
   'providers' => [
       // Should see:
       Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider::class,
   ]
   ```

---

### 9. ❌ "View file not found" After Publishing

**If you published views and they're not loading:**

1. Check published location:
   ```
   resources/views/vendor/chat-widget/
   ```

2. Clear view cache:
   ```bash
   php artisan view:clear
   ```

3. Clear compiled files:
   ```bash
   php artisan optimize:clear
   ```

---

### 10. ✅ Quick Verification Checklist

Run this to verify everything is set up correctly:

```bash
# 1. Check package is installed
composer show microrepairnet/chat-widget

# 2. Check migrations
php artisan migrate:status | grep chat

# 3. Check routes
php artisan route:list | grep api/chat

# 4. Check models exist
php artisan tinker
> Microrepairnet\ChatWidget\Models\LiveChat::all()

# 5. Check views
ls -la resources/views/vendor/chat-widget/
# or on Windows:
Get-ChildItem resources\views\vendor\chat-widget\
```

---

## Still Having Issues?

### Debug Steps:

1. **Check Browser Console (F12):**
   - Look for JavaScript errors
   - Verify CSRF token is present: `document.querySelector('meta[name="csrf-token"]')`
   - Check Network tab for failed requests

2. **Check Laravel Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Enable Debug Mode in .env:**
   ```env
   APP_DEBUG=true
   ```

4. **Run Diagnostic:**
   ```bash
   php artisan tinker
   
   # Check database
   > DB::table('live_chats')->count()
   
   # Check service provider
   > app(Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider::class)
   
   # Check config
   > config('chat-widget')
   ```

### Getting Help:

- **GitHub Issues:** https://github.com/jorodriguezpr/chat-widget/issues
- **Check Documentation:** 
  - [INSTALLATION.md](INSTALLATION.md)
  - [QUICK_START.md](QUICK_START.md)
  - [README.md](README.md)

---

## Quick Fix Commands (Copy & Paste)

```bash
# Complete reset (if nothing works)
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
composer dump-autoload

# Reinstall package
composer require microrepairnet/chat-widget --force

# Setup database
php artisan migrate

# Verify
php artisan route:list | grep api/chat
```
