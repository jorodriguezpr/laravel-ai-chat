<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Services\AI;

use Illuminate\Support\Facades\Http;

class GitHubModelsProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $model;
    private float $temperature;
    private int $maxTokens;

    public function __construct(string $apiKey, string $model = 'gpt-5.4', float $temperature = 0.7, int $maxTokens = 500)
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
                ->post('https://models.inference.ai.azure.com/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $this->temperature,
                    'max_tokens' => $this->maxTokens,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            throw new \Exception('GitHub Models API error: ' . $response->body());
        } catch (\Exception $e) {
            \Log::error('GitHub Models error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function validateCredentials(): bool
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->get('https://models.inference.ai.azure.com/models');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAvailableModels(): array
    {
        return [
            'gpt-5.4' => 'GPT-5.4 (Latest OpenAI)',
            'gpt-5.4-mini' => 'GPT-5.4 Mini (Fast)',
            'gpt-4o' => 'GPT-4o (Previous gen)',
            'deepseek-r1' => 'DeepSeek-R1 (Open reasoning)',
            'grok-3' => 'xAI Grok-3 (Powerful)',
            'grok-3-mini' => 'xAI Grok-3 Mini (Fast)',
            'llama-3-70b' => 'Llama 3 70B (Open-source)',
            'llama-3-8b' => 'Llama 3 8B (Fast)',
            'mistral-large' => 'Mistral Large',
            'phi-4' => 'Phi 4 (Efficient)',
        ];
    }
}
