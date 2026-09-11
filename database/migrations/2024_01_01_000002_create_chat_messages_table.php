<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tableName;
    protected $liveChatsTable;

    public function __construct()
    {
        $this->tableName = config('chat-widget.table_names.chat_messages', 'chat_messages');
        $this->liveChatsTable = config('chat-widget.table_names.live_chats', 'live_chats');
    }

    public function up(): void
    {
        if (!Schema::hasTable($this->tableName)) {
            Schema::create($this->tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('live_chat_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('sender_type')->default('visitor');
                $table->longText('message');
                $table->boolean('is_read')->default(false);
                $table->timestamps();

                $table->foreign('live_chat_id')->references('id')->on($this->liveChatsTable)->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
