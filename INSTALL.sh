#!/bin/bash
# Quick Install Script for Chat Widget Package

echo "Installing Chat Widget Package..."

# Add to composer.json
php <<'PHP'
$composer = json_decode(file_get_contents('composer.json'), true);
if (!isset($composer['repositories'])) {
    $composer['repositories'] = [];
}
// Add path repository if not exists
$hasPathRepo = false;
foreach ($composer['repositories'] as $repo) {
    if (isset($repo['type']) && $repo['type'] === 'path' && strpos($repo['url'], 'chat-widget') !== false) {
        $hasPathRepo = true;
        break;
    }
}
if (!$hasPathRepo) {
    $composer['repositories'][] = [
        'type' => 'path',
        'url' => './packages/microrepairnet/laravel-ai-chat'
    ];
}
file_put_contents('composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "✓ Updated composer.json\n";
PHP

# Require package
composer require microrepairnet/laravel-ai-chat

# Publish config and assets
php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-config"
php artisan vendor:publish --provider="Microrepairnet\ChatWidget\Providers\ChatWidgetServiceProvider" --tag="chat-widget-migrations"

# Run migrations
php artisan migrate

echo ""
echo "✓ Chat Widget Package installed successfully!"
echo ""
echo "Next steps:"
echo "1. Configure AI provider in .env:"
echo "   AI_CHAT_ENABLED=true"
echo "   AI_CHAT_PROVIDER=openai (or claude, gemini, github)"
echo ""
echo "2. Go to /admin/ai-settings to add your API credentials"
echo ""
echo "3. Include the widget in your layouts:"
echo "   @include('chat-widget::widget')"
echo ""
echo "4. Visit /admin/live-chat to see conversations"
echo ""
echo "For more info, see: packages/microrepairnet/laravel-ai-chat/README.md"
