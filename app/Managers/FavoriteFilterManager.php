<?php

declare(strict_types=1);

namespace App\Managers;

use App\Jobs\SendEmailJob;
use App\Mail\Offer\NewOffers;
use App\Models\FavoriteFilter;
use App\Services\SearchService;
use Illuminate\Support\Facades\Auth;

class FavoriteFilterManager
{
    public function getList(int $userId = null)
    {
        $userId = $userId ?: Auth::id();

        return FavoriteFilter::where('user_id', $userId)->paginate(config('dazu.pagination.per_page'));
    }

    /**
     * @param array $filters
     * @param int $period
     * @param bool $notification
     * @param string|null $userId
     * @return FavoriteFilter
     */
    public function store(array $filters, int $period, bool $notification, string $userId = null): FavoriteFilter
    {
        $userId = $userId ?: Auth::id();

        $filter = new FavoriteFilter();
        $filter->notification = $notification;
        $filter->period  = $period;
        $filter->filters = json_encode($filters);
        $filter->user_id = $userId;
        $filter->save();

        return $filter;
    }

    public function updateNotifications(int $id,bool $status)
    {
        return FavoriteFilter::where('id', $id)->update(['notification' => $status]);

        // return $filter->save();
    }

    /**
     * @param FavoriteFilter $filter
     * @return mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        $obj = FavoriteFilter::where('id',$id);
        return $obj->delete();
    }

    public function sendNotification(FavoriteFilter $filter)
    {
        $offers = resolve(SearchService::class)->search($filter->filters);
        dispatch(new SendEmailJob(new NewOffers($offers, $filter->user)));
    }
}
