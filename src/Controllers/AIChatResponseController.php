<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Controllers;

use Microrepairnet\ChatWidget\Models\LiveChat;
use Microrepairnet\ChatWidget\Models\ChatMessage;
use Microrepairnet\ChatWidget\Models\AIChatSetting;
use Microrepairnet\ChatWidget\Services\AI\AIChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AIChatResponseController extends Controller
{
    /**
     * Check if AI is enabled
     */
    public function checkAIStatus(): JsonResponse
    {
        $aiService = new AIChatService();
        $settings = AIChatSetting::current();

        return response()->json([
            'success' => true,
            'ai_enabled' => $aiService->isEnabled(),
            'show_talk_to_human' => $settings->enabled,
        ]);
    }

    /**
     * Get AI response for a message
     */
    public function getAIResponse(Request $request, int $chat): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Manually fetch chat model
        $chatModel = LiveChat::find($chat);
        if (!$chatModel) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        // Check if AI is enabled
        $aiService = new AIChatService();
        if (!$aiService->isEnabled()) {
            return response()->json([
                'success' => false,
                'error' => 'AI is not enabled',
            ], 400);
        }

        try {
            // Get conversation history (last 10 messages for context)
            $history = $chatModel->messages()
                ->whereIn('sender_type', [ChatMessage::SENDER_VISITOR, ChatMessage::SENDER_AGENT, ChatMessage::SENDER_AI])
                ->latest('created_at')
                ->limit(10)
                ->get(['message', 'sender_type'])
                ->reverse()
                ->toArray();

            // Get AI response
            $aiResponse = $aiService->sendMessage($validated['message'], $history);

            // Save AI response
            $aiMessage = $chatModel->addMessage($aiResponse, ChatMessage::SENDER_AI);

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $aiMessage->id,
                    'message' => $aiMessage->message,
                    'sender_type' => $aiMessage->sender_type,
                    'created_at' => $aiMessage->created_at,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Response Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get AI response',
            ], 500);
        }
    }

    /**
     * Request to talk with a human
     */
    public function requestHuman(Request $request, int $chat): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        // Manually fetch chat model
        $chatModel = LiveChat::find($chat);
        if (!$chatModel) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        // Add system message that human was requested
        $message = $validated['message'] ?? '';
        $systemMessage = "Visitor requested to talk with a human" . ($message ? ": $message" : "");

        $chatModel->addMessage($systemMessage, ChatMessage::SENDER_SYSTEM);

        // Mark chat as pending for agent to pick up
        if ($chatModel->status !== LiveChat::STATUS_CLOSED) {
            $chatModel->update(['status' => LiveChat::STATUS_PENDING]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Human support has been requested. An agent will be with you shortly.',
        ]);
    }
}
