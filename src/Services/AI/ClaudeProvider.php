<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Services\AI;

use Illuminate\Support\Facades\Http;

class ClaudeProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $model;
    private float $temperature;
    private int $maxTokens;

    public function __construct(string $apiKey, string $model = 'claude-sonnet-5', float $temperature = 0.7, int $maxTokens = 500)
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->temperature = $temperature;
        $this->maxTokens = $maxTokens;
    }

    public function sendMessage(string $message, string $systemPrompt, array $conversationHistory = []): string
    {
        $messages = [];

        // Add conversation history
        foreach ($conversationHistory as $msg) {
            $messages[] = [
                'role' => $msg['sender_type'] === 'visitor' ? 'user' : 'assistant',
                'content' => $msg['message'],
            ];
        }

        // Add current message
        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => $this->maxTokens,
                'system' => $systemPrompt,
                'messages' => $messages,
                'temperature' => $this->temperature,
            ]);

            if ($response->successful()) {
                return $response->json('content.0.text');
            }

            throw new \Exception('Claude API error: ' . $response->body());
        } catch (\Exception $e) {
            \Log::error('Claude error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function validateCredentials(): bool
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => 10,
                'messages' => [['role' => 'user', 'content' => 'test']],
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAvailableModels(): array
    {
        return [
            'claude-sonnet-5' => 'Claude Sonnet 5 (Latest - Best balance)',
            'claude-opus-5' => 'Claude Opus 5 (Most intelligent)',
            'claude-fable-5-1' => 'Claude Fable 5.1 (Most capable - advanced reasoning)',
            'claude-haiku-4-5-20251001' => 'Claude Haiku 4.5 (Fastest)',
            'claude-opus-4-6' => 'Claude Opus 4.6 (Previous gen)',
            'claude-sonnet-4-6' => 'Claude Sonnet 4.6 (Previous gen)',
            'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet (Legacy)',
            'claude-3-5-opus-20250514' => 'Claude 3.5 Opus (Legacy)',
            'claude-3-opus-20240229' => 'Claude 3 Opus (Legacy)',
            'claude-3-haiku-20240307' => 'Claude 3 Haiku (Legacy)',
        ];
    }
}
