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

    public function __construct()
    {
        $this->tableName = config('chat-widget.table_names.live_chat_settings', 'live_chat_settings');
    }

    public function up(): void
    {
        if (!Schema::hasTable($this->tableName)) {
            Schema::create($this->tableName, function (Blueprint $table) {
                $table->id();
                $table->string('setting_key')->unique();
                $table->text('setting_value')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });

            // Insert default settings
            DB::table($this->tableName)->insert([
                [
                    'setting_key' => 'welcome_message',
                    'setting_value' => 'Welcome to our chat! How can we help you?',
                    'description' => 'Welcome message shown when chat opens',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'setting_key' => 'offline_message',
                    'setting_value' => 'We are currently offline. Please leave a message and we will respond as soon as possible.',
                    'description' => 'Message shown when all agents are offline',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'setting_key' => 'widget_title',
                    'setting_value' => 'Chat with us',
                    'description' => 'Title displayed in the chat widget',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
