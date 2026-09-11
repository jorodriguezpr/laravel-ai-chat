<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Services\AI;

use Microrepairnet\ChatWidget\Models\AIChatSetting;
use Microrepairnet\ChatWidget\Models\AIProviderCredential;

class AIChatService
{
    private ?AIProviderInterface $provider = null;
    private AIChatSetting $settings;
    private $app;

    public function __construct($app = null)
    {
        $this->app = $app;
        $this->settings = AIChatSetting::current();
        $this->initializeProvider();
    }

    /**
     * Initialize the active AI provider
     */
    private function initializeProvider(): void
    {
        if (!$this->settings->enabled) {
            return;
        }

        $credential = AIProviderCredential::where('provider', $this->settings->provider)
            ->where('is_active', true)
            ->first();

        if (!$credential) {
            \Log::warning("No active credentials found for provider: {$this->settings->provider}");
            return;
        }

        $apiKey = $credential->api_key;
        $model = $credential->model ?? 'default';

        $this->provider = match($this->settings->provider) {
            'openai' => new OpenAIProvider($apiKey, $model, $this->settings->temperature, $this->settings->max_tokens),
            'claude' => new ClaudeProvider($apiKey, $model, $this->settings->temperature, $this->settings->max_tokens),
            'gemini' => new GeminiProvider($apiKey, $model, $this->settings->temperature, $this->settings->max_tokens),
            'github' => new GitHubModelsProvider($apiKey, $model, $this->settings->temperature, $this->settings->max_tokens),
            default => null,
        };
    }

    /**
     * Check if AI is enabled and ready
     */
    public function isEnabled(): bool
    {
        return $this->settings->enabled && $this->provider !== null;
    }

    /**
     * Send a message to the AI
     */
    public function sendMessage(string $message, array $conversationHistory = []): string
    {
        if (!$this->isEnabled()) {
            throw new \Exception('AI chat is not enabled');
        }

        return $this->provider->sendMessage(
            $message,
            $this->settings->system_prompt,
            $conversationHistory
        );
    }

    /**
     * Get current settings
     */
    public function getSettings(): AIChatSetting
    {
        return $this->settings;
    }

    /**
     * Get available providers
     */
    public static function getAvailableProviders(): array
    {
        return [
            'openai' => 'OpenAI (ChatGPT)',
            'claude' => 'Claude (Anthropic)',
            'gemini' => 'Gemini (Google)',
            'github' => 'GitHub Models',
        ];
    }
}
