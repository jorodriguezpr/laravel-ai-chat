<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Services\AI;

interface AIProviderInterface
{
    /**
     * Send a message to the AI provider and get a response
     */
    public function sendMessage(string $message, string $systemPrompt, array $conversationHistory = []): string;

    /**
     * Validate provider credentials
     */
    public function validateCredentials(): bool;

    /**
     * Get available models for this provider
     */
    public function getAvailableModels(): array;
}
