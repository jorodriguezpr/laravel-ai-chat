<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Microrepairnet\ChatWidget\Models\ChatMessage;

class LiveChat extends Model
{
    protected $table = 'live_chats';

    protected $fillable = [
        'user_id',
        'agent_id',
        'visitor_email',
        'visitor_name',
        'visitor_ip',
        'status',
        'is_agent_online',
    ];

    protected $casts = [
        'is_agent_online' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';

    /**
     * Get the user/visitor who started this chat
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the agent assigned to this chat
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get all messages in this chat
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'live_chat_id', 'id')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Add a new message to this chat
     */
    public function addMessage(string $message, string $senderType = 'visitor', ?int $userId = null): ChatMessage
    {
        return $this->messages()->create([
            'message' => $message,
            'sender_type' => $senderType,
            'user_id' => $userId,
        ]);
    }

    /**
     * Mark chat as active
     */
    public function markActive(): void
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Mark chat as closed
     */
    public function markClosed(): void
    {
        $this->update(['status' => self::STATUS_CLOSED]);
    }

    /**
     * Get unread message count
     */
    public function getUnreadCount(): int
    {
        return $this->messages()->where('is_read', false)->count();
    }
}
