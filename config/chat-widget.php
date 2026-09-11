<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Chat Widget Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the chat widget behavior and appearance
    |
    */

    'enabled' => env('CHAT_WIDGET_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Admin Panel
    |--------------------------------------------------------------------------
    |
    | The URL prefix and middleware used for the bundled admin routes
    | (live chat management + AI settings). Add your own authentication/
    | authorization middleware here to protect these routes in production.
    |
    */

    'admin_route_prefix' => env('CHAT_WIDGET_ADMIN_PREFIX', 'admin'),
    'admin_middleware' => ['web'],

    'table_names' => [
        'live_chats' => 'live_chats',
        'chat_messages' => 'chat_messages',
        'ai_chat_settings' => 'ai_chat_settings',
        'ai_provider_credentials' => 'ai_provider_credentials',
    ],

    'ai' => [
        'enabled' => env('AI_CHAT_ENABLED', false),
        'default_provider' => env('AI_CHAT_PROVIDER', 'openai'),

        'providers' => [
            'openai' => [
                'name' => 'OpenAI',
                'base_url' => 'https://api.openai.com/v1',
                'timeout' => 30,
            ],
            'claude' => [
                'name' => 'Anthropic Claude',
                'base_url' => 'https://api.anthropic.com/v1',
                'timeout' => 30,
            ],
            'gemini' => [
                'name' => 'Google Gemini',
                'base_url' => 'https://generativelanguage.googleapis.com/v1',
                'timeout' => 30,
            ],
            'github' => [
                'name' => 'GitHub Models',
                'base_url' => 'https://models.inference.ai.azure.com',
                'timeout' => 30,
            ],
        ],
    ],

    'widget' => [
        'position' => env('CHAT_WIDGET_POSITION', 'bottom-right'),
        'theme' => env('CHAT_WIDGET_THEME', 'light'),
        'title' => env('CHAT_WIDGET_TITLE', 'Chat with us'),
        'subtitle' => env('CHAT_WIDGET_SUBTITLE', 'We typically reply in minutes'),
    ],

    'message_polling_interval' => 2000, // milliseconds
    'max_message_length' => 1000,
    'max_conversation_history' => 10,
];
