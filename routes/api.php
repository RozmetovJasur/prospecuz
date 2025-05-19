<?php

use Illuminate\Support\Facades\Route;

Route::post('/telegram/webhook', [\App\Http\Controllers\Api\TelegramBotController::class, 'index']);
