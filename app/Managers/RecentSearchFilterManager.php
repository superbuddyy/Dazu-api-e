<?php

declare(strict_types=1);

namespace App\Managers;

use App\Jobs\SendEmailJob;
use App\Mail\Offer\NewOffers;
use App\Models\RecentSearchFilter;
use App\Services\SearchService;
use Illuminate\Support\Facades\Auth;

class RecentSearchFilterManager
{
    public function getList(int $userId = null)
    {
        $userId = $userId ?: Auth::id();

        return RecentSearchFilter::where('user_id', $userId)->orderBy('id','DESC')->paginate(config('dazu.pagination.per_page'));
    }
    public function getCount() {
        $userId = Auth::id();
        return RecentSearchFilter::where('user_id', $userId)
        ->count();
    }
    /**
     * @param array $filters
     * @param int $period
     * @param bool $notification
     * @param string|null $userId
     * @return RecentSearchFilter
     */
    public function store(array $filters, int $period, bool $notification, string $userId = null): RecentSearchFilter
    {
        $userId = $userId ?: Auth::id();
        $encoded_txt = base64_encode(json_encode($filters));
        $count = RecentSearchFilter::where('encoded_txt',$encoded_txt)->where('user_id',$userId)->delete();
        $filter = new RecentSearchFilter();
        $filter->notification = $notification;
        $filter->period  = $period;
        $filter->filters = json_encode($filters);
        $filter->encoded_txt = $encoded_txt;
        $filter->user_id = $userId;
        $filter->save();

        return $filter;
    }

    public function updateNotifications(int $id,bool $status)
    {
        return RecentSearchFilter::where('id', $id)->update(['notification' => $status]);

        // return $filter->save();
    }

    /**
     * @param RecentSearchFilter $filter
     * @return mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        $obj = RecentSearchFilter::where('id',$id);
        return $obj->delete();
    }
    public function deleteRemains($isone = true,$cnt = 0)
    {
        $userId = Auth::id();
        $object = RecentSearchFilter::where(['user_id' => $userId]);
        if ($isone) {
            $object->orderBy('id','ASC');
            $object->limit($cnt);
        }
        return $object->delete();
    }
    public function sendNotification(RecentSearchFilter $filter)
    {
        $offers = resolve(SearchService::class)->search($filter->filters);
        dispatch(new SendEmailJob(new NewOffers($offers, $filter->user)));
    }
}
