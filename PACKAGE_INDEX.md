# Chat Widget Package - Complete Documentation Index

## 📦 Package Information

- **Name**: microrepairnet/chat-widget
- **Version**: 1.1.0
- **License**: MIT
- **Author**: Jose Rodriguez Arroyo (jrpcone@gmail.com)
- **Homepage**: https://www.microrepair.net
- **Repository**: https://github.com/jorodriguezpr/laravel-ai-chat
- **Laravel Support**: 10.x, 11.x, 13.x
- **PHP Requirement**: 8.1+

---

## 📚 Documentation Files

### Getting Started

1. **[README.md](README.md)** ⭐ START HERE
   - Overview of all features
   - Quick 3-step installation
   - Requirements and compatibility
   - Basic usage examples
   - Configuration options

2. **[QUICK_START.md](QUICK_START.md)** ⚡ 3-MINUTE SETUP
   - Fastest path to working installation
   - Auto-loaded features list
   - CSRF token requirement (critical!)
   - Basic customization options

3. **[INSTALLATION.md](INSTALLATION.md)** 📖 DETAILED GUIDE
   - Step-by-step installation for Windows/Linux/Mac
   - Publishing options (config, views, controllers, etc.)
   - Environment configuration
   - Admin routes setup

4. **[INSTALLATION_CHECKLIST.md](INSTALLATION_CHECKLIST.md)** ✅ COMPREHENSIVE
   - Complete pre-installation checklist
   - All installation methods (Packagist, Git, Satis, local)
   - Database setup verification
   - Frontend integration testing
   - AI configuration walkthrough
   - Admin panel setup
   - Security checklist
   - Testing procedures
   - Troubleshooting common issues

### Deployment & Distribution

5. **[DEPLOYMENT.md](DEPLOYMENT.md)** 🚀 PRODUCTION GUIDE
   - System requirements and server specs
   - Production installation methods
   - Environment configuration
   - Security checklist (HTTPS, CSRF, rate limiting)
   - Performance optimization (caching, queues, CDN)
   - Monitoring & logging setup
   - Backup & recovery procedures
   - CI/CD pipeline example
   - Maintenance schedule

6. **[COMPOSER_SETUP.md](COMPOSER_SETUP.md)** 📦 REPOSITORY SETUP
   - Distribution methods comparison
   - **Option 1**: Public Packagist (free, open-source)
   - **Option 2**: Private Repository (Satis)
   - **Option 3**: Git Repository (direct)
   - Semantic versioning guide
   - Release process
   - Testing package installation
   - Troubleshooting composer issues

### Features & Usage

7. **[USAGE_EXAMPLES.md](USAGE_EXAMPLES.md)** 💡 CODE EXAMPLES
   - Widget integration examples
   - Model usage (LiveChat, ChatMessage)
   - AI service integration
   - Programmatic message sending
   - Provider configuration

8. **[CHANGELOG.md](CHANGELOG.md)** 📝 VERSION HISTORY
   - Version 1.1.0 changes (current release)
   - Added features
   - Changed/improved features
   - Bug fixes
   - Security enhancements
   - Upgrade guide from 1.0.x

### Troubleshooting

9. **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** 🔧 COMMON ISSUES
   - "View not found" errors
   - CSRF token missing
   - Messages not sending
   - Database connection issues
   - Route 404 errors
   - AI provider issues

---

## 🗂️ Package Structure

```
chat-widget/
├── composer.json                    # Package metadata, autoload config
├── LICENSE                          # MIT license
│
├── Documentation (9 files)
│   ├── README.md                    ⭐ Start here
│   ├── QUICK_START.md               ⚡ 3-minute setup
│   ├── INSTALLATION.md              📖 Detailed install guide
│   ├── INSTALLATION_CHECKLIST.md    ✅ Complete checklist
│   ├── DEPLOYMENT.md                🚀 Production deployment
│   ├── COMPOSER_SETUP.md            📦 Package distribution
│   ├── USAGE_EXAMPLES.md            💡 Code examples
│   ├── CHANGELOG.md                 📝 Version history
│   └── TROUBLESHOOTING.md           🔧 Common issues
│
├── config/
│   └── chat-widget.php              # Configuration file
│
├── database/
│   └── migrations/                  # 5 migration files
│       ├── *_create_live_chats_table.php
│       ├── *_create_chat_messages_table.php
│       ├── *_create_live_chat_settings_table.php
│       ├── *_create_ai_chat_settings_table.php
│       └── *_create_ai_provider_credentials_table.php
│
├── resources/
│   └── views/
│       ├── admin/                   # Admin panel views
│       │   ├── ai-settings.blade.php
│       │   ├── live-chat.blade.php
│       │   ├── live-chat-show.blade.php
│       │   └── ai-settings/
│       ├── components/
│       │   └── chat-widget.blade.php
│       ├── layouts/
│       │   └── admin.blade.php      # Admin layout
│       └── widget.blade.php          # Main widget entry
│
├── routes/
│   └── web.php                      # Package routes
│
└── src/
    ├── Controllers/                 # 5 controllers
    │   ├── AIChatResponseController.php
    │   ├── AISettingsController.php
    │   ├── ContactController.php
    │   ├── LiveChatAgentController.php
    │   └── LiveChatWidgetController.php
    │
    ├── Models/                      # 6 models
    │   ├── AIChatSetting.php
    │   ├── AIProviderCredential.php
    │   ├── ChatMessage.php
    │   ├── ContactMessage.php
    │   ├── LiveChat.php
    │   └── LiveChatSetting.php
    │
    ├── Providers/
    │   └── ChatWidgetServiceProvider.php
    │
    └── Services/
        └── AI/                      # AI Integration
            ├── AIChatService.php
            ├── AIProviderInterface.php
            ├── ClaudeProvider.php
            ├── GeminiProvider.php
            ├── GitHubModelsProvider.php
            └── OpenAIProvider.php
```

---

## ✨ Features Overview

### Core Features

- ✅ **Real-time Chat Widget** - Beautiful, responsive chat bubble interface
- ✅ **Multi-Provider AI Support** - OpenAI, Claude, Gemini, GitHub Models
- ✅ **Agent Management** - Pickup, respond to, and close chats
- ✅ **Live Polling** - Real-time message synchronization (3-second intervals)
- ✅ **Chat Status Management** - Pending → Active → Closed workflow
- ✅ **Admin Dashboard** - Comprehensive interface for managing chats
- ✅ **AI Settings Panel** - Configure providers, models, and API keys
- ✅ **System Messages** - Automatic notifications (agent joined, chat closed)
- ✅ **CSV Export** - Download chat history and audit trail

### AI Providers & Models (2026 Current)

#### OpenAI
- gpt-5.4 (default)
- gpt-5.4-mini
- gpt-5.4-nano
- gpt-4o
- gpt-4o-mini
- o1

#### Claude (Anthropic)
- claude-sonnet-4-6 (default)
- claude-opus-4-6
- claude-haiku-4-5-20251001

#### Gemini (Google)
- gemini-2.5-flash (default)
- gemini-2.5-pro
- gemini-2.5-flash-lite
- gemini-3.1-pro-preview
- gemini-3-flash-preview

#### GitHub Models
- gpt-5.4 (default)
- gpt-5.4-mini
- deepseek-r1
- grok-3
- grok-3-mini
- llama-3-70b
- mistral-large
- phi-4

---

## 🚀 Quick Installation

### Method 1: From Packagist (When Published)

```bash
composer require microrepairnet/chat-widget
php artisan migrate
```

Add to your layout:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('chat-widget::widget')
```

Done! ✅

### Method 2: From Git Repository

Add to `composer.json`:
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

Install:
```bash
composer require microrepairnet/chat-widget:^1.1
php artisan migrate
```

---

## 🔑 Key Routes

### Visitor/Public Routes

- `POST /api/chat/initiate` - Start new chat
- `POST /api/chat/{chat}/message` - Send visitor message
- `GET /api/chat/{chat}/messages` - Get messages (polling)
- `POST /api/chat/{chat}/close` - Close chat

### AI Routes

- `GET /api/ai/status` - Check if AI is enabled
- `POST /api/ai/{chat}/response` - Get AI response
- `POST /api/ai/{chat}/request-human` - Request human agent

### Admin Routes

- `GET /admin/live-chat` - List all chats (tabs: Pending, Active, Closed)
- `GET /admin/live-chat/{chat}` - View specific chat
- `POST /admin/live-chat/{chat}/message` - Send agent message
- `POST /admin/live-chat/{chat}/pickup` - Pickup pending chat
- `POST /admin/live-chat/{chat}/close` - Close chat
- `GET /admin/live-chat/api/{chat}/messages` - Polling for agents
- `GET /admin/live-chat/export/csv` - Export chat history

### Settings Routes

- `GET /admin/ai-settings` - AI configuration panel
- `POST /admin/ai-settings` - Update AI settings
- `POST /admin/ai-settings/test` - Test API credentials

---

## 📊 Database Tables

After running `php artisan migrate`, these tables are created:

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `live_chats` | Chat sessions | id, visitor_name, visitor_email, status, created_at |
| `chat_messages` | All messages | id, live_chat_id, sender_type, message, created_at |
| `live_chat_settings` | Widget settings | enabled, position, theme, title, subtitle |
| `ai_chat_settings` | AI configuration | enabled, provider, model, system_prompt, temperature |
| `ai_provider_credentials` | API keys | provider, api_key (encrypted) |

---

## 🎯 Use Cases

### Use Case 1: Website Support Chat

- Install widget on all pages
- Enable AI for instant responses
- Agents pickup when needed
- Export conversations for training

### Use Case 2: AI-First Customer Service

- Configure AI with custom system prompt
- AI handles 80% of inquiries automatically
- Agents only handle complex cases
- Monitor AI performance and costs

### Use Case 3: Lead Generation

- Collect visitor info via chat
- AI qualifies leads with questions
- Agent closes the sale
- Export leads to CRM

### Use Case 4: Multi-Site Deployment

- Install package on multiple Laravel projects via Composer
- Centralized AI configuration
- Consistent visitor experience
- Single update process

---

## 🔒 Security Features

- ✅ **CSRF Protection** - All forms protected
- ✅ **Encrypted Credentials** - API keys stored encrypted
- ✅ **Input Validation** - All user input sanitized
- ✅ **Rate Limiting** - Prevent abuse (configurable)
- ✅ **HTTPS Ready** - Production SSL support
- ✅ **XSS Prevention** - Output escaped in Blade templates
- ✅ **SQL Injection Protection** - Eloquent ORM used throughout

---

## 🛠️ Configuration Options

### Environment Variables

```env
# Widget Configuration
CHAT_WIDGET_ENABLED=true
CHAT_WIDGET_POSITION=bottom-right
CHAT_WIDGET_THEME=light
CHAT_WIDGET_TITLE="Chat with us"
CHAT_WIDGET_SUBTITLE="We typically reply in minutes"

# AI Configuration
AI_CHAT_ENABLED=true
AI_CHAT_PROVIDER=openai
```

### Config File (config/chat-widget.php)

```php
return [
    'enabled' => env('CHAT_WIDGET_ENABLED', true),
    
    'widget' => [
        'position' => 'bottom-right', // bottom-left, top-right, top-left
        'theme' => 'light',           // dark
        'title' => 'Chat with us',
        'subtitle' => 'We typically reply in minutes',
        'auto_close_after_hours' => 24,
    ],
    
    'ai' => [
        'enabled' => env('AI_CHAT_ENABLED', false),
        'default_provider' => env('AI_CHAT_PROVIDER', 'openai'),
        'timeout' => 30,
        'rate_limit' => [
            'enabled' => true,
            'max_requests' => 60,
            'per_minutes' => 1,
        ],
    ],
];
```

---

## 📈 Performance Considerations

### Recommended Production Setup

- **Cache**: Redis for session/cache
- **Queue**: Redis for AI processing jobs
- **Database**: MySQL 8.0+ or MariaDB 10.5+
- **Web Server**: Nginx with PHP-FPM
- **PHP OpCache**: Enabled
- **Caching**: Config, route, and view caching enabled

### Scaling Guidelines

| Concurrent Chats | Recommended Resources |
|------------------|----------------------|
| 1-50 | 1 CPU, 2GB RAM |
| 50-200 | 2 CPU, 4GB RAM |
| 200-500 | 4 CPU, 8GB RAM |
| 500+ | Load balancer + multiple app servers |

---

## 🔄 Update Process

### Minor/Patch Updates

```bash
composer update microrepairnet/chat-widget
php artisan migrate
php artisan cache:clear
```

### Major Version Updates

1. Read [CHANGELOG.md](CHANGELOG.md) for breaking changes
2. Backup database
3. Update composer.json version constraint
4. Run `composer update`
5. Run migrations
6. Test thoroughly in staging
7. Deploy to production

---

## 🆘 Support & Resources

### Documentation

- **GitHub Repository**: https://github.com/jorodriguezpr/laravel-ai-chat
- **Issues**: https://github.com/jorodriguezpr/laravel-ai-chat/issues
- **Discussions**: https://github.com/jorodriguezpr/laravel-ai-chat/discussions

### Contact

- **Email**: jrpcone@gmail.com
- **Website**: https://www.microrepair.net

### Contributing

Contributions welcome! Please:
1. Fork the repository
2. Create feature branch
3. Make changes with tests
4. Submit pull request

---

## 📝 License

MIT License - see [LICENSE](LICENSE) file for details.

---

## 🎉 Success Criteria

After successful installation, you should have:

- ✅ Chat bubble visible on frontend
- ✅ Visitor can send messages
- ✅ AI responds (if enabled)
- ✅ Admin panel accessible
- ✅ Agent can pickup pending chats
- ✅ Messages sync in real-time
- ✅ Agent can close chats
- ✅ No errors in logs or console

---

**Package Version**: 1.1.0  
**Last Updated**: April 15, 2026  
**Total Documentation Files**: 9  
**Total Code Files**: 24 (5 controllers, 6 models, 6 AI services, 5 migrations, 2 providers)  
**Total Views**: 7 (widget, admin panel, AI settings, layouts)  
**Supported Laravel Versions**: 10.x - 13.x  
**PHP Requirement**: 8.1+

---

## 🗺️ Recommended Reading Order

### For Quick Installation (5 minutes)
1. [QUICK_START.md](QUICK_START.md)
2. [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md)

### For Complete Setup (30 minutes)
1. [README.md](README.md)
2. [INSTALLATION_CHECKLIST.md](INSTALLATION_CHECKLIST.md)
3. [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

### For Production Deployment
1. [DEPLOYMENT.md](DEPLOYMENT.md)
2. [COMPOSER_SETUP.md](COMPOSER_SETUP.md)
3. [CHANGELOG.md](CHANGELOG.md)

### For Package Distribution
1. [COMPOSER_SETUP.md](COMPOSER_SETUP.md)
2. [DEPLOYMENT.md](DEPLOYMENT.md)
3. [README.md](README.md)

---

**Ready to install? Start with [QUICK_START.md](QUICK_START.md)! 🚀**
