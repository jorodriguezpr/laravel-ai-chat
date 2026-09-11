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
        $this->tableName = config('chat-widget.table_names.ai_provider_credentials', 'ai_provider_credentials');
    }

    public function up(): void
    {
        if (!Schema::hasTable($this->tableName)) {
            Schema::create($this->tableName, function (Blueprint $table) {
                $table->id();
                $table->string('provider');
                $table->longText('api_key');
                $table->string('model')->nullable();
                $table->json('additional_config')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();

                $table->unique('provider');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
