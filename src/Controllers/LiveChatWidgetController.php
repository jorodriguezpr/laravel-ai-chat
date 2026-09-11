<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Controllers;

use Microrepairnet\ChatWidget\Models\LiveChat;
use Microrepairnet\ChatWidget\Models\ChatMessage;
use Microrepairnet\ChatWidget\Models\LiveChatSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class LiveChatWidgetController extends Controller
{
    /**
     * Create a new live chat session
     */
    public function initiate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'required|email|max:255',
        ]);

        // Check if there's already an active session for this visitor
        $existingChat = LiveChat::where('visitor_email', $validated['visitor_email'])
            ->where('status', '!=', LiveChat::STATUS_CLOSED)
            ->latest()
            ->first();

        if ($existingChat) {
            return response()->json([
                'success' => true,
                'chat_id' => $existingChat->id,
                'messages' => $existingChat->messages()->get(['id', 'message', 'sender_type', 'created_at']),
            ]);
        }

        // Create new chat session
        $chat = LiveChat::create([
            'visitor_name' => $validated['visitor_name'],
            'visitor_email' => $validated['visitor_email'],
            'visitor_ip' => $request->ip(),
            'status' => LiveChat::STATUS_PENDING,
        ]);

        // Get welcome message from settings
        $welcomeMessage = LiveChatSetting::get('welcome_message', 'Welcome! A support agent will be with you shortly.');

        // Add welcome message
        $chat->addMessage(
            $welcomeMessage,
            ChatMessage::SENDER_SYSTEM
        );

        return response()->json([
            'success' => true,
            'chat_id' => $chat->id,
            'messages' => $chat->messages()->get(['id', 'message', 'sender_type', 'created_at']),
        ]);
    }

    /**
     * Send a message from visitor
     */
    public function sendMessage(Request $request, int $chat): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Manually fetch the chat from database
        $chatModel = LiveChat::find($chat);

        if (!$chatModel) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        // Only allow sending messages if chat is not closed
        if ($chatModel->status === LiveChat::STATUS_CLOSED) {
            return response()->json(['error' => 'Chat is closed'], 400);
        }

        // Save visitor message
        $message = $chatModel->addMessage($validated['message'], ChatMessage::SENDER_VISITOR);

        // Mark chat as active
        if ($chatModel->status === LiveChat::STATUS_PENDING) {
            $chatModel->markActive();
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'created_at' => $message->created_at,
            ],
        ]);
    }

    /**
     * Get new messages for a chat
     */
    public function getMessages(Request $request, int $chat): JsonResponse
    {
        $chatModel = LiveChat::find($chat);

        if (!$chatModel) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        $since = $request->query('since');

        $query = $chatModel->messages();

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        $messages = $query->get(['id', 'message', 'sender_type', 'created_at']);

        // Mark new agent messages as read
        $chatModel->messages()
            ->where('sender_type', ChatMessage::SENDER_AGENT)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'status' => $chatModel->status,
        ]);
    }

    /**
     * Close chat from visitor side
     */
    public function close(Request $request, int $chat): JsonResponse
    {
        $chatModel = LiveChat::find($chat);

        if (!$chatModel) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        $chatModel->markClosed();

        return response()->json(['success' => true]);
    }
}
