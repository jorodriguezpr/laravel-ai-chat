<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Controllers;

use Microrepairnet\ChatWidget\Models\AIChatSetting;
use Microrepairnet\ChatWidget\Models\AIProviderCredential;
use Microrepairnet\ChatWidget\Services\AI\AIChatService;
use Microrepairnet\ChatWidget\Services\AI\OpenAIProvider;
use Microrepairnet\ChatWidget\Services\AI\ClaudeProvider;
use Microrepairnet\ChatWidget\Services\AI\GeminiProvider;
use Microrepairnet\ChatWidget\Services\AI\GitHubModelsProvider;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AISettingsController extends Controller
{
    /**
     * Show AI settings page
     */
    public function index(): View
    {
        $settings = AIChatSetting::current();
        $providers = AIChatService::getAvailableProviders();
        $credentials = AIProviderCredential::all();

        return view('chat-widget::admin.ai-settings', [
            'settings' => $settings,
            'providers' => $providers,
            'credentials' => $credentials,
        ]);
    }

    /**
     * Update AI settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => 'boolean',
            'provider' => 'required|in:openai,claude,gemini,github',
            'model' => 'required|string',
            'system_prompt' => 'required|string|max:2000',
            'temperature' => 'required|numeric|min:0|max:2',
            'max_tokens' => 'required|integer|min:10|max:4000',
        ]);

        $settings = AIChatSetting::current();
        $settings->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'AI settings updated successfully',
        ]);
    }

    /**
     * Save provider credentials
     */
    public function saveCredentials(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => 'required|in:openai,claude,gemini,github',
            'api_key' => 'nullable|string|min:10',
            'model' => 'required|string',
        ]);

        // Get existing credential if it exists
        $existingCredential = AIProviderCredential::where('provider', $validated['provider'])->first();

        // If no API key provided and no existing credential, require API key
        if (empty($validated['api_key']) && !$existingCredential) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required for new credentials.',
            ], 400);
        }

        // Use existing API key if not provided
        $apiKey = $validated['api_key'] ?? $existingCredential->api_key;

        $credential = AIProviderCredential::updateOrCreate(
            ['provider' => $validated['provider']],
            [
                'api_key' => $apiKey,
                'model' => $validated['model'],
                'is_active' => true,
            ]
        );

        // Test the credentials only if API key was provided (new or updated)
        if (!empty($validated['api_key'])) {
            $isValid = $this->testProviderCredentials($validated['provider'], $apiKey);

            if (!$isValid) {
                // Restore previous state if validation fails
                if ($existingCredential) {
                    $credential->update(['api_key' => $existingCredential->api_key]);
                } else {
                    $credential->delete();
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API credentials. Please check your API key.',
                ], 400);
            }
        }

        // Make this provider active
        AIProviderCredential::where('provider', '!=', $validated['provider'])->update(['is_active' => false]);
        $credential->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Provider credentials saved and validated successfully',
        ]);
    }

    /**
     * Get models for a provider
     */
    public function getProviderModels(Request $request): JsonResponse
    {
        $provider = $request->query('provider');

        $models = match($provider) {
            'openai' => (new OpenAIProvider(''))->getAvailableModels(),
            'claude' => (new ClaudeProvider(''))->getAvailableModels(),
            'gemini' => (new GeminiProvider(''))->getAvailableModels(),
            'github' => (new GitHubModelsProvider(''))->getAvailableModels(),
            default => [],
        };

        return response()->json([
            'success' => true,
            'models' => $models,
        ]);
    }

    /**
     * Test provider credentials
     */
    private function testProviderCredentials(string $provider, string $apiKey): bool
    {
        try {
            $providerInstance = match($provider) {
                'openai' => new OpenAIProvider($apiKey),
                'claude' => new ClaudeProvider($apiKey),
                'gemini' => new GeminiProvider($apiKey),
                'github' => new GitHubModelsProvider($apiKey),
                default => null,
            };

            return $providerInstance?->validateCredentials() ?? false;
        } catch (\Exception $e) {
            \Log::error("Provider validation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete provider credentials
     */
    public function deleteCredentials(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => 'required|in:openai,claude,gemini,github',
        ]);

        AIProviderCredential::where('provider', $validated['provider'])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Provider credentials deleted',
        ]);
    }
}
