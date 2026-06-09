<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('agent.perizinan.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('agent.document.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
