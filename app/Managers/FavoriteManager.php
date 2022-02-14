<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\Favorite;
use App\Models\Offer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class FavoriteManager
{
    /**
     * @param string|null $userId
     * @return LengthAwarePaginator
     */
    public function getList(string $userId = null): LengthAwarePaginator
    {
        $userId = $userId ?: Auth::id();
        return Offer::select(['offers.*', 'favorites.created_at as favorite_at'])
            ->join('favorites', 'offers.id', '=', 'favorites.offer_id')
            ->where('favorites.user_id', $userId)
            ->orderBy('favorite_at')
            ->paginate(config('dazu.pagination.per_page'));
    }

    /**
     * @param Offer $offer
     * @param string|null $userId
     * @return Favorite
     */
    public function store(Offer $offer, string $userId = null): Favorite
    {
        $userId = $userId ?: Auth::id();
        return Favorite::firstOrCreate([
            'offer_id' => $offer->id, 'user_id' => $userId],
            ['allow_notifications' => false]
        );
    }

    /**
     * @param Offer $offer
     * @param bool $status
     * @param bool|null $userId
     * @return bool
     */
    public function updateNotifications(Offer $offer, bool $status, bool $userId = null): bool
    {
        $userId = $userId ?: Auth::id();
        Favorite::where(['offer_id' => $offer->id, 'user_id' => $userId])->update(['allow_notifications' => $status]);

        return $offer->save();
    }

    /**
     * @param Offer $offer
     * @param string|null $userId
     * @return mixed
     */
    public function delete(Offer $offer, string $userId = null)
    {
        $userId = $userId ?: Auth::id();
        $object = Favorite::where(['offer_id' => $offer->id, 'user_id' => $userId])->firstOrFail();

        return $object->delete();
    }
}
