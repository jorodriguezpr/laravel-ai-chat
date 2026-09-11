<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Services\AI;

use Illuminate\Support\Facades\Http;

class GeminiProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $model;
    private float $temperature;
    private int $maxTokens;

    public function __construct(string $apiKey, string $model = 'gemini-3.8-flash', float $temperature = 0.7, int $maxTokens = 500)
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->temperature = $temperature;
        $this->maxTokens = $maxTokens;
    }

    public function sendMessage(string $message, string $systemPrompt, array $conversationHistory = []): string
    {
        $contents = [];
        $systemInstruction = ['text' => $systemPrompt];

        // Add conversation history
        foreach ($conversationHistory as $msg) {
            $contents[] = [
                'role' => $msg['sender_type'] === 'visitor' ? 'user' : 'model',
                'parts' => [['text' => $msg['message']]],
            ];
        }

        // Add current message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]],
        ];

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'systemInstruction' => $systemInstruction,
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => $this->temperature,
                    'maxOutputTokens' => $this->maxTokens,
                ],
            ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text');
            }

            throw new \Exception('Gemini API error: ' . $response->body());
        } catch (\Exception $e) {
            \Log::error('Gemini error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function validateCredentials(): bool
    {
        try {
            $response = Http::get("https://generativelanguage.googleapis.com/v1/models/gemini-3.8-flash?key={$this->apiKey}");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAvailableModels(): array
    {
        return [
            'gemini-3.8-flash' => 'Gemini 3.8 Flash (Latest - Recommended)',
            'gemini-3.1-pro-preview' => 'Gemini 3.1 Pro Preview (Most capable - complex reasoning)',
            'gemini-3.7-flash' => 'Gemini 3.7 Flash (Previous gen)',
            'gemini-3.6-flash' => 'Gemini 3.6 Flash (Previous gen)',
            'gemini-3.5-flash-lite' => 'Gemini 3.5 Flash Lite (Fastest, most cost-effective)',
            'gemini-2.5-flash' => 'Gemini 2.5 Flash (Legacy)',
            'gemini-2.5-pro' => 'Gemini 2.5 Pro (Legacy)',
            'gemini-2.5-flash-lite' => 'Gemini 2.5 Flash Lite (Legacy)',
        ];
    }
}
