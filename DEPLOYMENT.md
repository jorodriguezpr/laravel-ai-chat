# Production Deployment Guide

This guide covers deploying the Chat Widget package in production environments.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation Methods](#installation-methods)
3. [Production Configuration](#production-configuration)
4. [Security Checklist](#security-checklist)
5. [Performance Optimization](#performance-optimization)
6. [Monitoring & Logging](#monitoring--logging)
7. [Backup & Recovery](#backup--recovery)
8. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### System Requirements

- **PHP**: 8.1 or higher
- **Laravel**: 10.0, 11.0, or 13.0
- **Database**: MySQL 5.7+, MariaDB 10.3+, PostgreSQL 10+
- **Web Server**: Nginx or Apache with mod_rewrite
- **Composer**: 2.0+

### Recommended Server Specs (per 1000 concurrent users)

- **CPU**: 2+ cores
- **RAM**: 2GB minimum, 4GB recommended
- **Storage**: 10GB+ (for logs and database)
- **Network**: 100Mbps+ connection

---

## Installation Methods

### Method 1: Composer from Git Repository (Recommended)

#### Step 1: Add Repository to composer.json

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/jorodriguezpr/chat-widget"
        }
    ]
}
```

#### Step 2: Install Package

```bash
composer require microrepairnet/chat-widget:^1.1
```

#### Step 3: Run Migrations

```bash
php artisan migrate --force
```

#### Step 4: Publish Assets (Optional - for customization)

```bash
# Publish configuration only
php artisan vendor:publish --tag="chat-widget-config"

# Or publish everything for full customization
php artisan vendor:publish --tag="chat-widget" --force
```

#### Step 5: Clear Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Method 2: Private Packagist or Satis

If you're using a private package repository:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://your-packagist.com"
        }
    ]
}
```

Then install normally:

```bash
composer require microrepairnet/chat-widget
```

### Method 3: Local Package (Development)

For development or testing environments:

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

---

## Production Configuration

### Environment Variables

Add to your production `.env` file:

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Chat Widget
CHAT_WIDGET_ENABLED=true
CHAT_WIDGET_POSITION=bottom-right
CHAT_WIDGET_THEME=light
CHAT_WIDGET_TITLE="Chat with us"
CHAT_WIDGET_SUBTITLE="We typically reply in minutes"

# AI Configuration
AI_CHAT_ENABLED=true
AI_CHAT_PROVIDER=openai

# Database (use production credentials)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Cache (use Redis in production)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### config/chat-widget.php

If published, customize for production:

```php
<?php

return [
    'enabled' => env('CHAT_WIDGET_ENABLED', true),
    
    'ai' => [
        'enabled' => env('AI_CHAT_ENABLED', false),
        'default_provider' => env('AI_CHAT_PROVIDER', 'openai'),
        
        // Rate limiting for AI requests
        'rate_limit' => [
            'enabled' => true,
            'max_requests' => 60,
            'per_minutes' => 1,
        ],
        
        // Timeout settings
        'timeout' => 30, // seconds
    ],
    
    'widget' => [
        'position' => env('CHAT_WIDGET_POSITION', 'bottom-right'),
        'theme' => env('CHAT_WIDGET_THEME', 'light'),
        'title' => env('CHAT_WIDGET_TITLE', 'Chat with us'),
        'subtitle' => env('CHAT_WIDGET_SUBTITLE', 'We typically reply in minutes'),
        
        // Auto-close inactive chats after X hours
        'auto_close_after_hours' => 24,
    ],
    
    // Table name customization
    'table_names' => [
        'live_chats' => 'live_chats',
        'chat_messages' => 'chat_messages',
        'live_chat_settings' => 'live_chat_settings',
        'ai_chat_settings' => 'ai_chat_settings',
        'ai_provider_credentials' => 'ai_provider_credentials',
    ],
];
```

---

## Security Checklist

### ✅ Pre-Deployment Checks

- [ ] **HTTPS enforced** on production domain
- [ ] **CSRF protection** enabled (`meta name="csrf-token"` in layout)
- [ ] **Rate limiting** configured for API endpoints
- [ ] **Database credentials** secured and not in version control
- [ ] **AI API keys** stored in database (encrypted) or environment variables
- [ ] **File permissions** correctly set (755 for directories, 644 for files)
- [ ] **`.env` file** is not publicly accessible
- [ ] **Debug mode** disabled (`APP_DEBUG=false`)
- [ ] **Error logging** configured (not displayed to users)

### API Key Security

AI provider API keys are stored encrypted in the database. To add them:

1. Navigate to `/admin/ai-settings`
2. Select your AI provider
3. Enter API key and select model
4. Click "Save & Test Credentials"

Keys are automatically encrypted before storage.

### Additional Security Measures

```php
// routes/web.php - Add rate limiting
Route::middleware(['web', 'throttle:60,1'])->group(function () {
    // Chat widget routes
});
```

---

## Performance Optimization

### Database Indexing

Ensure these indexes are created for optimal performance:

```sql
-- On live_chats table
CREATE INDEX idx_status_created ON live_chats(status, created_at);
CREATE INDEX idx_visitor_email ON live_chats(visitor_email);

-- On chat_messages table
CREATE INDEX idx_chat_created ON chat_messages(live_chat_id, created_at);
CREATE INDEX idx_sender_type ON chat_messages(sender_type);
```

### Caching Strategy

Enable caching in production:

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

### Queue Configuration

For heavy AI processing, use queues:

```php
// .env
QUEUE_CONNECTION=redis
```

```bash
# Run queue worker
php artisan queue:work --tries=3 --timeout=60
```

### CDN Integration

Serve static assets via CDN:

```blade
{{-- In your layout --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
```

---

## Monitoring & Logging

### Laravel Telescope (Development/Staging)

```bash
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

### Error Tracking

Configure error tracking service (e.g., Sentry, Bugsnag):

```env
# .env
SENTRY_LARAVEL_DSN=https://your-sentry-dsn
```

### Log Monitoring

Monitor these log files:

- `storage/logs/laravel.log` - Application errors
- `storage/logs/chat-widget-*.log` - Package-specific logs

Key events to monitor:

- AI API failures
- Database connection errors
- Message send failures
- Route model binding errors

### Health Checks

Create a health check endpoint:

```php
// routes/api.php
Route::get('/health/chat-widget', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'ai_enabled' => config('chat-widget.ai.enabled'),
        'timestamp' => now()->toDateTimeString(),
    ]);
});
```

---

## Backup & Recovery

### Database Backups

Daily automated backups:

```bash
# Using mysqldump
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Or using Laravel backup package
composer require spatie/laravel-backup
php artisan backup:run
```

### Critical Tables

Ensure regular backups of:

- `live_chats`
- `chat_messages`
- `ai_provider_credentials`
- `ai_chat_settings`

### Recovery Procedure

1. Restore database backup
2. Run migrations: `php artisan migrate`
3. Clear all caches: `php artisan optimize:clear`
4. Restart queue workers: `php artisan queue:restart`

---

## Troubleshooting

### Common Production Issues

#### 1. "419 Page Expired" Errors

**Cause**: CSRF token mismatch

**Solution**:
```blade
{{-- Ensure this is in your layout head --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
```

#### 2. AI Not Responding

**Checks**:
- Verify API key in `/admin/ai-settings`
- Check logs: `storage/logs/laravel.log`
- Test credentials using "Test Credentials" button
- Verify provider service is operational

#### 3. Messages Not Polling

**Cause**: Route cache or incorrect endpoint

**Solution**:
```bash
php artisan route:clear
php artisan route:cache
```

#### 4. Database Connection Errors

**Checks**:
- Verify DB credentials in `.env`
- Check firewall rules
- Test connection: `php artisan tinker` → `DB::connection()->getPdo()`

#### 5. High Memory Usage

**Solutions**:
- Increase PHP memory_limit: `memory_limit = 256M`
- Enable query result chunking
- Use Redis for caching
- Configure queue workers with `--memory=128`

### Debug Mode (Temporary)

For critical production debugging only:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

**⚠️ Remember to disable after debugging!**

---

## Deployment Checklist

### Pre-Deployment

- [ ] Code reviewed and tested
- [ ] Database migrations tested
- [ ] Environment variables configured
- [ ] API keys added and tested
- [ ] Backups created
- [ ] SSL certificate verified

### Deployment

- [ ] Code deployed via CI/CD or manual
- [ ] Composer install: `composer install --no-dev --optimize-autoloader`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Clear and cache: `php artisan optimize`
- [ ] Restart services: `php artisan queue:restart`

### Post-Deployment

- [ ] Health check endpoint responding
- [ ] Chat widget visible on frontend
- [ ] Admin panel accessible
- [ ] AI responses working
- [ ] Message polling functional
- [ ] Logs monitored for errors
- [ ] Performance metrics within acceptable range

---

## CI/CD Pipeline Example

### GitHub Actions Workflow

```yaml
name: Deploy Chat Widget

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          
      - name: Install Dependencies
        run: composer install --prefer-dist --no-dev
        
      - name: Run Tests
        run: php artisan test
        
      - name: Deploy to Production
        run: |
          php artisan down
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          php artisan up
```

---

## Support & Maintenance

### Regular Maintenance Tasks

**Daily**:
- Monitor error logs
- Check AI provider status

**Weekly**:
- Review database size and performance
- Clean old closed chats (optional)
- Review AI usage and costs

**Monthly**:
- Update dependencies: `composer update`
- Review security patches
- Backup verification

### Getting Help

- **Documentation**: [README.md](README.md)
- **Issues**: GitHub Issues
- **Email**: support@microrepair.net
- **Emergency**: Contact development team

---

## Version Upgrades

### Minor Versions (1.x.y)

```bash
composer update microrepairnet/chat-widget
php artisan migrate
php artisan optimize:clear
```

### Major Versions (2.0.0+)

Follow upgrade guide in CHANGELOG.md for breaking changes.

---

**Last Updated**: April 15, 2026  
**Package Version**: 1.1.0  
**Supported Laravel Versions**: 10.x, 11.x, 13.x
