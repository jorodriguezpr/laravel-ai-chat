<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

use Illuminate\Support\Facades\Route;
use Microrepairnet\ChatWidget\Controllers\LiveChatAgentController;
use Microrepairnet\ChatWidget\Controllers\AISettingsController;

// Admin routes for the chat widget - prefix/middleware are configurable via
// config('chat-widget.admin_route_prefix') and config('chat-widget.admin_middleware').
Route::middleware(config('chat-widget.admin_middleware', ['web']))
    ->prefix(config('chat-widget.admin_route_prefix', 'admin'))
    ->name('admin.')
    ->group(function () {
        Route::prefix('live-chat')->name('live-chat.')->group(function () {
            Route::get('/', [LiveChatAgentController::class, 'index'])->name('index');
            Route::get('/settings', [LiveChatAgentController::class, 'settings'])->name('settings');
            Route::post('/settings', [LiveChatAgentController::class, 'updateSettings'])->name('settings.update');
            Route::get('/api/chats', [LiveChatAgentController::class, 'chats'])->name('chats');
            Route::get('/{chat}', [LiveChatAgentController::class, 'show'])->name('show');
            Route::post('/{chat}/message', [LiveChatAgentController::class, 'sendMessage'])->name('send-message');
            Route::post('/{chat}/pickup', [LiveChatAgentController::class, 'pickup'])->name('pickup');
            Route::post('/{chat}/close', [LiveChatAgentController::class, 'close'])->name('close');
            Route::get('/api/{chat}/messages', [LiveChatAgentController::class, 'getMessages'])->name('get-messages');
        });

        Route::prefix('ai-settings')->name('ai-settings.')->group(function () {
            Route::get('/', [AISettingsController::class, 'index'])->name('index');
            Route::get('/models', [AISettingsController::class, 'getProviderModels'])->name('models');
            Route::post('/update', [AISettingsController::class, 'updateSettings'])->name('update');
            Route::post('/save-credentials', [AISettingsController::class, 'saveCredentials'])->name('save-credentials');
            Route::post('/delete-credentials', [AISettingsController::class, 'deleteCredentials'])->name('delete-credentials');
        });
    });
