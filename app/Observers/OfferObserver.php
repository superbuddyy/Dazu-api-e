<?php

namespace App\Observers;

use App\Models\Offer;
use Carbon\Carbon;

class OfferObserver
{
    /**
     * Handle the offer "created" event.
     *
     * @param  Offer  $offer
     * @return void
     */
    public function created(Offer $offer)
    {
        $offer->expire_time = Carbon::now()->addHours(config('dazu.offer.expire_time'));
        $offer->save();
    }

    /**
     * Handle the offer "updated" event.
     *
     * @param  Offer  $offer
     * @return void
     */
    public function updated(Offer $offer)
    {
        if ($offer->isDirty('price')) {
            $offer->old_price = $offer->getOriginal('price');
        }
    }

    /**
     * Handle the offer "deleted" event.
     *
     * @param  Offer  $offer
     * @return void
     */
    public function deleted(Offer $offer)
    {
        //
    }

    /**
     * Handle the offer "restored" event.
     *
     * @param  Offer  $offer
     * @return void
     */
    public function restored(Offer $offer)
    {
        //
    }

    /**
     * Handle the offer "force deleted" event.
     *
     * @param  Offer  $offer
     * @return void
     */
    public function forceDeleted(Offer $offer)
    {
        //
    }
}
