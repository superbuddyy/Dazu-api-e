<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\Offer;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionManager
{
    public function handlePaymentCallback(array $cachedInfo): void
    {
        $subscription = Subscription::findOrFail($cachedInfo['subscription_id']);
        $offer = Offer::findOrFail($cachedInfo['offer_id']);
        $offer->subscriptions()->detach();
        $offer->subscriptions()
            ->attach(
                $cachedInfo['subscription_id'],
                ['end_date' => Carbon::now()->addHours($subscription->duration)]
            );
    }

    public function update(Subscription $subscription, array $data): Subscription
    {
        $subscription->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'] * 100,
            'duration' => $data['duration'],
            'number_of_refreshes' => $data['number_of_refreshes'],
            'refresh_price' => $data['refresh_price'] * 100,
            'number_of_raises' => $data['number_of_raises'],
            'raise_price' => $data['raise_price'] * 100,
            'featured_on_search_results_and_categories' => $data['featured_on_search_results_and_categories'],
            'featured_on_homepage' => $data['featured_on_homepage'],
        ]);

        return $subscription->refresh();
    }
}
