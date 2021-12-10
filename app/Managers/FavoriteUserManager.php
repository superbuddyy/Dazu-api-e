<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\FavoriteUsers;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class FavoriteUserManager
{
    /**
     * @param string|null $userId
     * @return LengthAwarePaginator
     */
    public function getList(string $role = '',string $userId = null): LengthAwarePaginator
    {

        $userId = $userId ?: Auth::id();
        if ($role != '') {
            return User::select(['users.*', 'favorite_users.created_at as favorite_at'])
                ->join('favorite_users', 'users.id', '=', 'favorite_users.fav_user_id')
                ->where('favorite_users.user_id', $userId)
                ->whereHas('roles', function($q) use ($role) { $q->where('name', $role); })
                ->orderBy('favorite_at')
                ->paginate(config('dazu.pagination.per_page'));
        } else {
            return User::select(['users.*', 'favorite_users.created_at as favorite_at'])
            ->join('favorite_users', 'users.id', '=', 'favorite_users.fav_user_id')
            ->where('favorite_users.user_id', $userId)
            ->orderBy('favorite_at')
            ->paginate(config('dazu.pagination.per_page'));
        }
    }

    public function getItem(string $userId = null)
    {
        $id = Auth::id();
        return FavoriteUsers::where('user_id',$id)->where('fav_user_id',$userId)->first();
    }

    /**
     * @param User $user
     * @param string|null $userId
     * @return FavoriteUsers
     */
    public function store(User $user, string $userId = null): FavoriteUsers
    {
        $userId = $userId ?: Auth::id();
        return FavoriteUsers::firstOrCreate([
            'fav_user_id' => $user->id, 'user_id' => $userId],
            ['allow_notifications' => false]
        );
    }

    /**
     * @param User $user
     * @param bool $status
     * @param bool|null $userId
     * @return bool
     */
    public function updateNotifications(User $user, bool $status, bool $userId = null): bool
    {
        $userId = $userId ?: Auth::id();
        FavoriteUsers::where(['fav_user_id' => $user->id, 'user_id' => $userId])->update(['allow_notifications' => $status]);

        return $user->save();
    }

    /**
     * @param User $user
     * @param string|null $userId
     * @return mixed
     */
    public function delete(User $user, string $userId = null)
    {
        $id = Auth::id();
        $object = FavoriteUsers::where(['fav_user_id' => $user->id, 'user_id' => $id])->firstOrFail();

        return $object->delete();
    }
}
