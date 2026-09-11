# Changelog

All notable changes to the Chat Widget package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- README screenshots (`docs/screenshots/`) of the visitor widget and the admin panel (live chat list, conversation view, AI settings), captured from a live install

## [1.2.0] - 2026-09-11

### Added
- `php artisan chat-widget:install` command - publishes config, adds missing `.env` keys, runs migrations, and prints next steps
- Admin routes (`/admin/live-chat`, `/admin/ai-settings`, and friends) now auto-load from the package itself via the service provider - no more manually creating `routes/admin.php`
- `config('chat-widget.admin_route_prefix')` and `config('chat-widget.admin_middleware')` to control the admin panel's URL prefix and middleware
- Missing `admin.live-chat.settings` / `admin.live-chat.settings.update` routes and a `chat-widget::admin.live-chat-settings` view for the previously-unreachable welcome-message settings page
- `pickup` admin route, matching the controller method and UI button that already existed
- Laravel 12 support in `composer.json`
- Author/license header on every source file

### Fixed
- All admin views are now resolved through the `chat-widget::` view namespace instead of unnamespaced names, so the package works immediately after `composer require` without copying any blade files into the consuming app (previously required per `CHAT_WIDGET_SETUP.md`'s manual steps)
- `AISettingsController` and `LiveChatAgentController` referenced views by the wrong path in two cases: `show()` resolved to a stale placeholder view instead of the full-featured one (pickup button, audit-log tab, CSV export), and the config key for GitHub Models (`github-models` vs `github`) was inconsistent between `config/chat-widget.php` and the rest of the codebase
- Removed duplicate/stale admin view files left over from an earlier UI iteration (`admin/live-chat/index.blade.php`, `admin/live-chat/show.blade.php`, `admin/live-chat-show.blade.php`'s previous stale content, `admin/ai-settings/index.blade.php`) that depended on a host-provided `layouts.app` and were not wired to any current route
- `ChatWidgetServiceProvider` singleton binding for `AIChatService` (see 1.1.1) confirmed still correct after the above cleanup

## [1.1.1] - 2026-09-11

### Changed
- Updated Claude provider defaults to Sonnet 5 (was Sonnet 4.6); added Opus 5 and Fable 5.1 as selectable models
- Updated OpenAI provider defaults to GPT-6 Astra (was GPT-5.4); added the GPT-5.6 Sol/Terra/Luna tier
- Updated Gemini provider defaults to Gemini 3.8 Flash (was 2.5 Flash); added Gemini 3.1 Pro Preview, 3.7/3.6 Flash, 3.5 Flash Lite
- Previous-generation models kept in each provider's model list for backward compatibility with existing credentials

### Fixed
- `ChatWidgetServiceProvider` imported `Services\AIChatService` instead of `Services\AI\AIChatService`, which would fatal-error the moment the container resolved the singleton binding (dormant bug — controllers instantiate the service directly rather than via the container)

### Note
- GitHub Models provider was left unchanged — its catalog IDs come from GitHub's own model marketplace (not verifiable without authenticated access at the time of this update); verify current catalog IDs there before bumping its defaults

## [1.1.0] - 2026-04-15

### Added
- Multi-provider AI support (OpenAI GPT-5.4, Claude Sonnet 4.6, Gemini 2.5, GitHub Models)
- Agent pickup functionality for pending chats
- Real-time message polling between visitor and agent
- AI settings page with model selection
- Model field in AI chat settings
- Audit trail view with CSV export functionality
- System messages for chat lifecycle (agent joined, chat closed)
- Route model binding bypass for compatibility
- Comprehensive error logging

### Changed
- Updated OpenAI provider to GPT-5.4 series (2026 models)
- Updated Claude provider to Sonnet 4.6 and Opus 4.6
- Updated Gemini provider to 2.5 Flash and 3.1 Pro Preview
- Improved AI provider credential management
- Enhanced admin interface with pending/active/closed chat tabs
- Better message display with sender type indicators
- Improved validation for AI settings

### Fixed
- 403 Forbidden errors on message sending (removed email verification)
- 500 Internal Server Error due to null live_chat_id (fixed route model binding)
- Route model binding failures across all controllers
- AI response endpoint 403 errors
- Admin polling endpoint 404 errors
- Close method typo in LiveChatWidgetController
- Route name mismatches in admin views
- Missing model field in ai_chat_settings table

### Security
- Encrypted API key storage for AI providers
- CSRF token protection on all endpoints
- Input validation on all user-submitted data

## [1.0.0] - 2024-12-10

### Added
- Initial release
- Basic chat widget functionality
- Live chat management interface
- Message persistence
- Real-time polling system
- CSRF protection
- Responsive design
- Customizable widget positioning
- Admin interface for chat management
- Basic AI chat support (OpenAI only)

### Features
- Visitor chat initiation
- Agent response system
- Chat status management (pending, active, closed)
- Message history
- Bootstrap 5 UI
- Laravel 10+ support

---

## Version Compatibility

| Chat Widget | Laravel | PHP    |
|------------|---------|--------|
| 1.1.x      | 10-13   | 8.1+   |
| 1.0.x      | 10-11   | 8.1+   |

## Upgrade Guide

### From 1.0.x to 1.1.0

#### Database Migration Required

```bash
php artisan migrate
```

This will add the `model` column to the `ai_chat_settings` table.

#### Configuration Changes

1. AI provider models have been updated. Clear your cache:
```bash
php artisan cache:clear
php artisan config:clear
```

2. Update your AI credentials in the admin panel at `/admin/ai-settings` to select the latest models.

#### Breaking Changes

None. Version 1.1.0 is backward compatible with 1.0.x.

#### New Features to Try

1. **Agent Pickup**: Pending chats now show a "Pickup Chat" button
2. **Updated AI Models**: Access to GPT-5.4, Claude 4.6, Gemini 2.5
3. **Better Admin Interface**: Improved chat list with AI/Agent indicators
4. **CSV Export**: Export conversation logs from the audit trail

---

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines.

## Support

For issues, questions, or feature requests, please open an issue on GitHub or contact support@microrepair.net.
