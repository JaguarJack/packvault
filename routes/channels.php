<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Package;

# package 构建通知
Broadcast::channel('package-build-notification.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('package-build-output.{id}', function ($user, $id) {
    return true;
});
