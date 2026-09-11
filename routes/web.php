<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

use Illuminate\Support\Facades\Route;
use Microrepairnet\ChatWidget\Controllers\LiveChatWidgetController;
use Microrepairnet\ChatWidget\Controllers\AIChatResponseController;

// Chat Widget API Routes (Public - No authentication required)
Route::prefix('api/chat')->name('chat.')->group(function () {
    Route::post('/initiate', [LiveChatWidgetController::class, 'initiate'])->name('initiate');
    Route::post('/{chat}/message', [LiveChatWidgetController::class, 'sendMessage'])->name('send-message');
    Route::get('/{chat}/messages', [LiveChatWidgetController::class, 'getMessages'])->name('get-messages');
    Route::post('/{chat}/close', [LiveChatWidgetController::class, 'close'])->name('close');
});

// AI Chat Response Routes (Public - No authentication required)
Route::prefix('api/ai')->name('ai.')->group(function () {
    Route::get('/status', [AIChatResponseController::class, 'checkAIStatus'])->name('status');
    Route::post('/{chat}/response', [AIChatResponseController::class, 'getAIResponse'])->name('response');
    Route::post('/{chat}/request-human', [AIChatResponseController::class, 'requestHuman'])->name('request-human');
});

