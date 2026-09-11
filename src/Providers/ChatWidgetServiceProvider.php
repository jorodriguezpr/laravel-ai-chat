<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Providers;

use Illuminate\Support\ServiceProvider;
use Microrepairnet\ChatWidget\Console\Commands\InstallCommand;
use Microrepairnet\ChatWidget\Services\AI\AIChatService;

class ChatWidgetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge default config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/chat-widget.php',
            'chat-widget'
        );

        // Register AI Chat Service as singleton
        $this->app->singleton(AIChatService::class, function ($app) {
            return new AIChatService($app);
        });
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'chat-widget');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }

        // === CONFIGURATION ===
        $this->publishes([
            __DIR__ . '/../../config/chat-widget.php' => config_path('chat-widget.php'),
        ], 'chat-widget-config');

        // === VIEWS ===
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/chat-widget'),
        ], 'chat-widget-views');

        // === MIGRATIONS ===
        $this->publishes([
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'chat-widget-migrations');

        // === CONTROLLERS - Allow users to customize ===
        $this->publishes([
            __DIR__ . '/../Controllers' => app_path('Http/Controllers/ChatWidget'),
        ], 'chat-widget-controllers');

        // === ROUTES - Allow users to customize ===
        $this->publishes([
            __DIR__ . '/../../routes/web.php' => base_path('routes/chat-widget.php'),
            __DIR__ . '/../../routes/admin.php' => base_path('routes/chat-widget-admin.php'),
        ], 'chat-widget-routes');

        // === MODELS - Allow users to extend/customize ===
        $this->publishes([
            __DIR__ . '/../Models' => app_path('Models/ChatWidget'),
        ], 'chat-widget-models');

        // === ALL (convenience tag for publishing everything) ===
        $this->publishes([
            __DIR__ . '/../../config/chat-widget.php' => config_path('chat-widget.php'),
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/chat-widget'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
            __DIR__ . '/../Controllers' => app_path('Http/Controllers/ChatWidget'),
            __DIR__ . '/../../routes/web.php' => base_path('routes/chat-widget.php'),
            __DIR__ . '/../Models' => app_path('Models/ChatWidget'),
        ], 'chat-widget');
    }
}
