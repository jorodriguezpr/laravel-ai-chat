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
        $this->tableName = config('chat-widget.table_names.ai_chat_settings', 'ai_chat_settings');
    }

    public function up(): void
    {
        if (!Schema::hasTable($this->tableName)) {
            Schema::create($this->tableName, function (Blueprint $table) {
                $table->id();
                $table->boolean('enabled')->default(false);
                $table->string('provider')->default('openai');
                $table->string('model')->default('gpt-6-astra');
                $table->longText('system_prompt')->default('You are a helpful customer support assistant. Be professional, friendly, and provide accurate information. If you cannot help, suggest contacting a human agent.');
                $table->float('temperature')->default(0.7);
                $table->integer('max_tokens')->default(500);
                $table->timestamps();
            });

            \DB::table($this->tableName)->insert([
                'enabled' => false,
                'provider' => 'openai',
                'model' => 'gpt-6-astra',
                'system_prompt' => 'You are a helpful customer support assistant. Be professional, friendly, and provide accurate information. If you cannot help, suggest contacting a human agent.',
                'temperature' => 0.7,
                'max_tokens' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
