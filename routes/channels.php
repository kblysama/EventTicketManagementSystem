<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('events', fn () => true);

Broadcast::channel('user.{id}', function (User $user, int $id) {
    return $user->id === $id;
});

Broadcast::channel('organizer.{id}', function (User $user, int $id) {
    return $user->id === $id && ($user->isOrganizer() || $user->isAdmin());
});

Broadcast::channel('admin', function (User $user) {
    return $user->role === Role::Admin;
});
