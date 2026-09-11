<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Models;

use Illuminate\Database\Eloquent\Model;

class AIChatSetting extends Model
{
    protected $table = 'ai_chat_settings';
    protected $fillable = ['enabled', 'provider', 'model', 'system_prompt', 'temperature', 'max_tokens'];
    protected $casts = [
        'enabled' => 'boolean',
        'temperature' => 'float',
        'max_tokens' => 'integer',
    ];

    /**
     * Get the current settings
     */
    public static function current()
    {
        return self::first() ?? self::create([
            'enabled' => false,
            'provider' => 'openai',
            'model' => 'gpt-6-astra',
            'system_prompt' => 'You are a helpful customer support assistant. Be professional, friendly, and provide accurate information. If you cannot help, suggest contacting a human agent.',
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);
    }
}
