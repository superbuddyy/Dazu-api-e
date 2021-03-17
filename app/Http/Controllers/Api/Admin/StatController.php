<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class StatController extends Controller
{
    const CACHE_ADMIN_STATS_KEY = 'admin_general_stats';

    public function index ()
    {
        $stats = Redis::get(self::CACHE_ADMIN_STATS_KEY);
        if ($stats !== null) {
            $stats = json_decode($stats, true);
        } else {
            $stats = [
                'users' => User::all()->count(),
                'all_offers' => Offer::all()->count(),
                'active_offers' => Offer::where('status', OfferStatus::ACTIVE)->where('expire_time', '>', Carbon::now())->count(),
                'subscriptions' => DB::table('offer_subscriptions')->count(),
            ];

            Redis::set(
                self::CACHE_ADMIN_STATS_KEY,
                json_encode($stats),
                'EX',
                '600'
            );
        }

        return response()->success($stats);
    }
}
