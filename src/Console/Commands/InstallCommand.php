<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'chat-widget:install
        {--force : Overwrite any existing published config file}
        {--no-migrate : Skip running the package migrations}';

    protected $description = 'Install the Chat Widget package (config, migrations, and .env keys)';

    public function handle(): int
    {
        $this->components->info('Installing Chat Widget');

        $this->publishConfig();
        $this->ensureEnvKeys();

        if (! $this->option('no-migrate')) {
            $this->runMigrations();
        }

        $this->printNextSteps();

        return self::SUCCESS;
    }

    protected function publishConfig(): void
    {
        $this->callSilent('vendor:publish', [
            '--provider' => 'Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider',
            '--tag' => 'chat-widget-config',
            '--force' => $this->option('force'),
        ]);

        $this->components->task('Published config/chat-widget.php');
    }

    protected function runMigrations(): void
    {
        $this->components->task('Running migrations', function () {
            $this->callSilent('migrate');

            return true;
        });
    }

    protected function ensureEnvKeys(): void
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            return;
        }

        $contents = File::get($envPath);
        $defaults = [
            'CHAT_WIDGET_ENABLED' => 'true',
            'AI_CHAT_ENABLED' => 'false',
            'AI_CHAT_PROVIDER' => 'openai',
        ];

        $missing = [];
        foreach ($defaults as $key => $value) {
            if (! preg_match('/^' . preg_quote($key, '/') . '=/m', $contents)) {
                $missing[] = "{$key}={$value}";
            }
        }

        if (empty($missing)) {
            return;
        }

        File::append($envPath, "\n# Chat Widget\n" . implode("\n", $missing) . "\n");
        $this->components->task('Added missing .env keys (' . implode(', ', array_keys($defaults)) . ')');
    }

    protected function printNextSteps(): void
    {
        $this->newLine();
        $this->components->info('Chat Widget installed. Next steps:');

        $this->line('  1. Add the widget to any page your visitors use:');
        $this->line("     <fg=cyan>@include('chat-widget::widget')</>");
        $this->newLine();

        $prefix = config('chat-widget.admin_route_prefix', 'admin');
        $this->line('  2. Manage live chats and configure AI providers at:');
        $this->line("     <fg=cyan>/{$prefix}/live-chat</> and <fg=cyan>/{$prefix}/ai-settings</>");
        $this->newLine();

        $this->line('  3. Protect the admin routes with your own authentication middleware');
        $this->line("     by setting <fg=cyan>'admin_middleware'</> in config/chat-widget.php");
        $this->newLine();

        $this->line('  4. Add an AI provider API key from the AI Settings page (OpenAI, Claude,');
        $this->line('     Gemini, or GitHub Models) and enable AI responses when ready.');
    }
}
