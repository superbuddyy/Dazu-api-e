<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\Offer;
use App\Models\Subscription;
use Carbon\Carbon;
use App\Enums\OfferStatus;

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
        $sub_data = $cachedInfo['subscriptions'] ?? null;
        if ($cachedInfo['subscriptions'] && $sub_data) {
            // $has_raise_one = $sub_data['has_raise_one'] ?? $offer->has_raise_one;
            // $has_raise_three = $sub_data['has_raise_three'] ?? $offer->has_raise_three;
            // $has_raise_ten = $sub_data['has_raise_ten'] ?? $offer->has_raise_ten;
            // $is_bargain = $sub_data['is_bargain'] ?? $offer->is_bargain;
            // $is_urgent = $sub_data['is_urgent'] ?? $offer->is_urgent;
            $total_raises = $offer->total_raises;

            if (isset($sub_data['is_bargain']) && !empty($sub_data['is_bargain'])) {
                $is_bargain = $sub_data['is_bargain'];
            } else {
                $is_bargain = $offer->is_bargain;
            }

            if (isset($sub_data['is_urgent']) && !empty($sub_data['is_urgent'])) {
                $is_urgent = $sub_data['is_urgent'];
            } else {
                $is_urgent = $offer->is_urgent;
            }

            if (isset($sub_data['has_raise_one']) && !empty($sub_data['has_raise_one'])) { 
                $total_raises = $total_raises + 1;
                $has_raise_one = $sub_data['has_raise_one'];
            } else {
                $has_raise_one = $offer->has_raise_one;
            }

            if (isset($sub_data['has_raise_three']) && !empty($sub_data['has_raise_three'])) {
                $total_raises = $total_raises + 3;
                $has_raise_three = $sub_data['has_raise_three'];
            } else {
                $has_raise_three = $offer->has_raise_three;
            }

            if (isset($sub_data['has_raise_ten']) && !empty($sub_data['has_raise_ten'])) {
                $total_raises = $total_raises + 10;
                $has_raise_ten = $sub_data['has_raise_ten'];
            } else {
                $has_raise_ten = $offer->has_raise_ten;
            }
            $offer_status = $offer->status;
            if ($offer->status === OfferStatus::IN_ACTIVE_BY_USER || $offer->status === OfferStatus::EXPIRED) {
                $offer_status = OfferStatus::ACTIVE;
            }
            $offer->update([
                'total_raises' => $total_raises,
                'has_raise_one' => $has_raise_one,
                'has_raise_three' => $has_raise_three,
                'has_raise_ten' => $has_raise_ten,
                'is_bargain' => $is_bargain,
                'is_urgent' => $is_urgent,
                'status' => $offer_status
            ]);
        }
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
            // 'raise_price_three' => $data['raise_price_three'] * 100,
            'raise_price_ten' => $data['raise_price_ten'] * 100,
            'urgent_price' => $data['urgent_price'] * 100,
            'bargain_price' => $data['bargain_price'] * 100,
            'featured_on_search_results_and_categories' => $data['featured_on_search_results_and_categories'],
            'featured_on_homepage' => $data['featured_on_homepage'],
        ]);

        return $subscription->refresh();
    }
}
