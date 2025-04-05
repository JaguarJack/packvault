<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;

Route::get('/', [IndexController::class, 'home'])->middleware(['auth', 'verified'])->name('home');

# 其他模块功能
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
# PackVault 模块
require __DIR__.'/packvault.php';
require __DIR__.'/webhook.php';
# License 模块
require __DIR__.'/license.php';
