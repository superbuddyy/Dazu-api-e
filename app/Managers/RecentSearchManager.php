<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\RecentSearch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class RecentSearchManager
{
    /**
     * @param string|null $userId
     * @return LengthAwarePaginator
     */
    public function getList(string $userId = null): LengthAwarePaginator
    {
        $userId = $userId ?: Auth::id();
        return RecentSearch::where('user_id', $userId)
            ->orderBy('id','DESC')
            ->paginate(config('dazu.pagination.per_page'));
    }
    public function getCount() {
        $userId = Auth::id();
        return RecentSearch::where('user_id', $userId)
        ->count();
    }
    /**
     * @param Offer $offer
     * @param string|null $userId
     * @return Favorite
     */
    public function store(string $display_name,string $lat, string $lon,string $userId = null): RecentSearch
    {
        $userId = $userId ?: Auth::id();
        $count = RecentSearch::where('display_name',$display_name)->where('lat',$lat)->where('lon',$lon)->where('user_id',$userId)->delete();
        return RecentSearch::firstOrCreate([
            'display_name' => $display_name ?? '', 
            'user_id' => $userId,
            'lat' => $lat ?? '',
            'lon' => $lon ?? ''
            ],
        );
        
    }

    /**
     * @param Offer $offer
     * @param string|null $userId
     * @return mixed
     */
    public function delete($isone = true,$cnt = 0)
    {
        $userId = Auth::id();
        $object = RecentSearch::where(['user_id' => $userId]);
        if ($isone) {
            $object->orderBy('id','ASC');
            $object->limit($cnt);
        }
        return $object->delete();
    }
}
