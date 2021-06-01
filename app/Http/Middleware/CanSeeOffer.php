<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\OfferStatus;
use App\Laravue\Acl;
use App\Models\Offer;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CanSeeOffer
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $offer = $request->offer;
        if ($request->has('preview') && $this->verifyOfferToken($offer)) {
            return $next($request);
        }

        $user = Auth::user();
        $userIds = [];
        if ($user && $user->getRoleName() === Acl::ROLE_COMPANY && $user->company) {
            $userIds = User::where('company_id', $user->company_id)->pluck('id')->all();
        } else if($user) {
            $userIds = [$user->id];
        }
        if (!in_array($offer->user_id, $userIds) && ($offer->status !== OfferStatus::ACTIVE || $offer->isExpired)){
            throw new ModelNotFoundException();
        }

        return $next($request);
    }

    public function verifyOfferToken(Offer $offer): bool
    {
        if (Cache::get('offer-token:'. $offer->id) === $_COOKIE['offer-token']) {
            Auth::login($offer->user);
            return true;
        }

        return false;
    }
}
