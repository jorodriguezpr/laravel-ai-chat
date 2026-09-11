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
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class LiveChatAgentController extends Controller
{
    /**
     * Show all pending chats for agents
     */
    public function index(): View
    {
        $pendingChats = LiveChat::where('status', LiveChat::STATUS_PENDING)
            ->with('messages')
            ->orderBy('created_at', 'asc')
            ->get();

        $activeChats = LiveChat::where('status', LiveChat::STATUS_ACTIVE)
            ->with('messages')
            ->orderBy('updated_at', 'desc')
            ->get();

        $closedChats = LiveChat::where('status', LiveChat::STATUS_CLOSED)
            ->with('messages')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('chat-widget::admin.live-chat', [
            'pendingChats' => $pendingChats,
            'activeChats' => $activeChats,
            'closedChats' => $closedChats,
        ]);
    }

    /**
     * Show a specific chat conversation
     */
    public function show(LiveChat $chat): View
    {
        $messages = $chat->messages()->get();

        return view('chat-widget::admin.live-chat-show', [
            'chat' => $chat,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message as agent
     */
    public function sendMessage(Request $request, LiveChat $chat): JsonResponse
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            \Log::info('Agent sending message to chat', [
                'chat_id' => $chat->id,
                'message' => $validated['message'],
            ]);

            // Create message from agent
            $message = $chat->addMessage($validated['message'], ChatMessage::SENDER_AGENT);

            // Mark chat as active if it was pending
            if ($chat->status === LiveChat::STATUS_PENDING) {
                $chat->markActive();
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
        } catch (\Exception $e) {
            \Log::error('Error sending message:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get new messages for a chat
     */
    public function getMessages(Request $request, LiveChat $chat): JsonResponse
    {
        $since = $request->query('since');

        $query = $chat->messages();

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        $messages = $query->get(['id', 'message', 'sender_type', 'created_at']);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Close chat
     */
    public function close(Request $request, LiveChat $chat): JsonResponse
    {
        $chat->markClosed();

        $chat->addMessage(
            'Chat has been closed by the agent.',
            ChatMessage::SENDER_SYSTEM
        );

        return response()->json(['success' => true]);
    }

    /**
     * Pickup a pending chat
     */
    public function pickup(Request $request, LiveChat $chat): JsonResponse
    {
        if ($chat->status !== LiveChat::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'error' => 'Chat is not in pending status',
            ], 400);
        }

        // Mark chat as active
        $chat->markActive();

        // Add system message
        $chat->addMessage(
            'An agent has joined the chat.',
            ChatMessage::SENDER_SYSTEM
        );

        return response()->json([
            'success' => true,
            'message' => 'Chat picked up successfully',
        ]);
    }

    /**
     * Get all chats (for dashboard)
     */
    public function chats(): JsonResponse
    {
        $chats = LiveChat::with('messages')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'visitor_name' => $chat->visitor_name,
                    'visitor_email' => $chat->visitor_email,
                    'status' => $chat->status,
                    'created_at' => $chat->created_at,
                    'updated_at' => $chat->updated_at,
                    'message_count' => $chat->messages()->count(),
                    'last_message' => $chat->messages()->latest()->first()?->message,
                ];
            });

        return response()->json([
            'success' => true,
            'chats' => $chats,
        ]);
    }

    /**
     * Show live chat settings page
     */
    public function settings(): View
    {
        $welcomeMessage = LiveChatSetting::get('welcome_message', '');

        return view('chat-widget::admin.live-chat-settings', [
            'welcomeMessage' => $welcomeMessage,
        ]);
    }

    /**
     * Update live chat settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'welcome_message' => 'required|string|max:1000',
        ]);

        LiveChatSetting::set('welcome_message', $validated['welcome_message'], 'Welcome message shown to visitors');

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
        ]);
    }
}
