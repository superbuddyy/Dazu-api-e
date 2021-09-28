<?php

declare(strict_types=1);

namespace App\Services\Billings;

use App\Enums\AvatarType;
use App\Enums\TransactionStatus;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Transaction;

class OfferBillingService
{
    /** @var Offer */
    private $offer;

    /** @var int */
    private $billAmount;

    /** @var array */
    private $details;

    /** @var array */
    private $transactionsData;


    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
        $this->details = [];
        $this->transactionsData = [];
        $this->billAmount = 0;
    }

    public function calculateBill()
    {
        $this->getTransactions();
        $this->calculateSubscriptionPrice();
        $this->calculateLinksPrice();
        $this->calculatePhotosPrice();
        $this->calculateUrgentPrice();
        $this->calculateAvatarPrice();
        $this->calculateVideoAvatarPrice();
        $this->calculateVisibleFromDate();

        return [
            'billAmount' => $this->billAmount,
            'details' => $this->details,
        ];
    }

    private function getTransactions()
    {
        $transactions = Transaction::where('offer_id', $this->offer->id)->where('status', TransactionStatus::PAID)->get();
        foreach ($transactions as $transaction) {
            foreach ($transaction->line_items as $line_item) {
                if (!array_key_exists($line_item['id'], $this->transactionsData)) {
                    $this->transactionsData[$line_item['id']] = ['qty' => $line_item['qty'], 'price' => $line_item['price']];
                } else {
                    $this->transactionsData[$line_item['id']]['qty'] += $line_item['qty'];
                    $this->transactionsData[$line_item['id']]['price'] += $line_item['price'];
                }
            }
        }
    }

    private function calculateLinksPrice()
    {
        $linkPrice = Setting::where('name', 'link.price')->first();
        $price = 0;
        $linksCount = 0;
        foreach ($this->offer->links as $link) {
            if ($link !== null) {
                $price += $linkPrice->value;
                $linksCount++;
            }
        }

        $isPaid = false;
        if (isset($this->transactionsData[$linkPrice->id])) {
            $linksCount = $linksCount - $this->transactionsData[$linkPrice->id]['qty'];
            $price = $linksCount > 0 ? $linksCount * $linkPrice->value : 0;
            $isPaid = $linksCount === 0 ;
        }

        if ($price > 0 && !$isPaid) {
            $this->details['links'] = [
                'name' => $linksCount . ' linki do zewnętrzynch stron',
                'value' => $price,
                'id' => $linkPrice->id,
                'qty' => $linksCount,
            ];
            $this->billAmount += $price;
        }
    }

    private function calculatePhotosPrice()
    {
        $photoPrice = Setting::where('name', 'photo.price')->first();
        $price = 0;
        $photosCount = $this->offer->photos->count();
        if ($photosCount > 3) {
            $photosCount = ($photosCount - 3);
            $price += $photosCount * $photoPrice->value;
        }

        $isPaid = false;
        if (isset($this->transactionsData[$photoPrice->id])) {
            $photosCount = $photosCount - $this->transactionsData[$photoPrice->id]['qty'];
            $price = $photosCount > 0 ? $photosCount * $photoPrice->value : 0;
            $isPaid = $photosCount === 0 ;
        }

        if ($price > 0 && !$isPaid) {
            $this->details['photos'] = [
                'name' => $photosCount . ' dodatkowe zdjęcia',
                'value' => $price,
                'id' => $photoPrice->id,
                'qty' => $photosCount,
            ];
            $this->billAmount += $price;
        }
    }

    private function calculateUrgentPrice()
    {
        $urgentPrice = Setting::where('name', 'urgent.price')->first();

        $isPaid = false;
        if (isset($this->transactionsData[$urgentPrice->id])) {
            $isPaid = true;
        }

        $urgentAttr = $this->offer->attributes->where('id', 20);
        if ($urgentAttr->isNotEmpty() && $urgentAttr->first()->result === 'true' && !$isPaid) {
            $this->details['urgent'] = [
                'name' => 'Oznaczenie "pilne"',
                'value' => $urgentPrice->value,
                'id' => $urgentPrice->id,
            ];
            $this->billAmount += $urgentPrice->value;
        }
    }

    private function calculateSubscriptionPrice()
    {
        //TODO:: Check is already paid
        /** @var Subscription $subscription */
        $subscription = $this->offer->activeSubscription;
        if ($subscription->id !== Subscription::FREE) {
            $this->details['subscription'] = [
                'name' => $subscription->name,
                'value' => $subscription->price,
            ];
            $this->billAmount += $subscription->price;
        }


        $pivotData = $this->offer->subscriptions()->get(['urgent'])[0]['pivot'];

        if ($pivotData['urgent'] === 1) {
            $this->billAmount += $subscription->urgent_price;
            $this->details['pilne'] = [
                'name' => 'Pilne',
                'value' => $subscription->urgent_price,
            ];
        }

        if ($pivotData['bargain'] === 1) {
            $this->billAmount += $subscription->bargain_price;
            $this->details['okazja'] = [
                'name' => 'Okazja',
                'value' => $subscription->bargain_price,
                'id' => $subscription->id,
            ];
        }
    }

    private function calculateAvatarPrice()
    {
        $user = $this->offer->user;
        $avatar = $user->avatars->where('type', AvatarType::PHOTO)->where('in_active', false)->first();
        if (!$avatar) {
            return;
        }
        $avatarPrice = Setting::where('name', 'company_avatar.price')->first();
        if (!$avatar->is_active) {
            $this->details['avatar'] = [
                'name' => 'Avatar',
                'value' => $avatarPrice->value,
                'id' => $avatarPrice->id,
            ];
            $this->billAmount += $avatarPrice->value;
        }
    }

    private function calculateVideoAvatarPrice()
    {
        $videoPrice = Setting::where('name', 'avatar_video_url.price')->first();

        if (!$this->offer->user->company) {
            return;
        }
        $user = $this->offer->user;
        $avatar = $user->avatars->where('type', AvatarType::VIDEO_URL)->where('in_active', false)->first();
        if (!$avatar) {
            return;
        }
        if (!$avatar->is_active) {
            $this->details['avatar'] = [
                'name' => 'Wideo avatar',
                'value' => $videoPrice->value,
                'id' => $videoPrice->id,
            ];
            $this->billAmount += $videoPrice->value;
        }
    }

    private function calculateVisibleFromDate()
    {
        $setting = Setting::where('name', 'visible_from_date.price')->first();

        $isPaid = false;
        if (isset($this->transactionsData[$setting->id])) {
            $isPaid = true;
        }

        if ($this->offer->visible_from_date !== null && !$isPaid) {
            $this->details['visible_from_date'] = [
                'name' => 'Ogłoszenie w innym terminie',
                'value' => $setting->value,
                'id' => $setting->id,
            ];
            $this->billAmount += $setting->value;
        }
    }
}
