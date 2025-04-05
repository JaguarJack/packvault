<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;


# webhook
Route::controller(WebhookController::class)->prefix('webhook')->group(function () {
    Route::post('github', 'github');
});
