<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Services\AI;

use Illuminate\Support\Facades\Http;

class OpenAIProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $model;
    private float $temperature;
    private int $maxTokens;

    public function __construct(string $apiKey, string $model = 'gpt-6-astra', float $temperature = 0.7, int $maxTokens = 500)
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->temperature = $temperature;
        $this->maxTokens = $maxTokens;
    }

    public function sendMessage(string $message, string $systemPrompt, array $conversationHistory = []): string
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

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
            $response = Http::withToken($this->apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $this->temperature,
                    'max_tokens' => $this->maxTokens,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            throw new \Exception('OpenAI API error: ' . $response->body());
        } catch (\Exception $e) {
            \Log::error('OpenAI error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function validateCredentials(): bool
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->get('https://api.openai.com/v1/models');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAvailableModels(): array
    {
        return [
            'gpt-6-astra' => 'GPT-6 Astra (Latest - Most capable)',
            'gpt-5.6-sol' => 'GPT-5.6 Sol (Complex professional work)',
            'gpt-5.6-terra' => 'GPT-5.6 Terra (Balanced intelligence & cost)',
            'gpt-5.6-luna' => 'GPT-5.6 Luna (Cost-sensitive workloads)',
            'gpt-5.4' => 'GPT-5.4 (Previous gen)',
            'gpt-5.4-mini' => 'GPT-5.4 Mini (Previous gen)',
            'gpt-4o' => 'GPT-4o (Legacy)',
            'gpt-4o-mini' => 'GPT-4o Mini (Legacy)',
        ];
    }
}
