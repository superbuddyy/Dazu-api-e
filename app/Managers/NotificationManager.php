<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\FavoriteFilter;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class NotificationManager
{
    public function getUserNotifications(User $user = null): Collection
    {
        return Notification::where('user_id', $user->id)->get();
    }

    public function store(string $message, string $link, string $type, string $userId): Notification
    {
        return Notification::create([
            'message' => $message,
            'link' => $link,
            'type' => $type,
            'user_id' => $userId,
        ]);
    }

    public function deactivate()
    {
        return Notification::where('user_id', Auth::id())->where('active', true)->update(['active' => false]);
    }
}
