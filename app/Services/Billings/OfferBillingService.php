<?php

declare(strict_types=1);

namespace App\Services\Billings;

use App\Enums\TransactionStatus;
use App\Laravue\Acl;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;

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
        $this->calculateVideoAvatarPrice();
        $this->calculateUrgentPrice();
        $this->calculateAvatarPrice();
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
        $subscription = $this->offer->activeSubscription;
        if ($subscription->id !== Subscription::FREE) {
            $this->details['subscription'] = [
                'name' => $subscription->name,
                'value' => $subscription->price,
            ];
            $this->billAmount += $subscription->price;
        }
    }

    private function calculateAvatarPrice()
    {
        $avatarPrice = Setting::where('name', 'company_avatar.price')->first();
        $user = $this->offer->user;
        if ($user->company) {
            $avatar = $user->company->avatar ?? false;
            $avatarExpireDate = $user->company->avatar_expire_date;
            $isAvatarAlreadyPaid = $avatarExpireDate != null && $avatarExpireDate > Carbon::now();
            if ($user->hasRole(ACL::ROLE_COMPANY) && $avatar && !$isAvatarAlreadyPaid) {
                $this->details['company_avatar'] = [
                    'name' => 'Avatar',
                    'value' => $avatarPrice->value,
                    'id' => $avatarPrice->id,
                ];
                $this->billAmount += $avatarPrice->value;
            }
        } else {
            if (!$user->avatar) {
                return;
            }
            $avatarExpireDate = $this->offer->user->avatar->expire_date;
            $isAvatarAlreadyPaid = $avatarExpireDate != null
                && $avatarExpireDate > Carbon::now()
                && $this->offer->user->avatar->is_active;
            if (!$this->offer->user->hasRole(ACL::ROLE_COMPANY) && !$isAvatarAlreadyPaid) {
                $this->details['company_avatar'] = [
                    'name' => 'Avatar',
                    'value' => $avatarPrice->value,
                    'id' => $avatarPrice->id,
                ];
                $this->billAmount += $avatarPrice->value;
            }
            return;
        }
    }

    private function calculateVideoAvatarPrice()
    {
        $videoPrice = Setting::where('name', 'video_avatar.price')->first();

        if ($this->offer->user->company) {
            $videoAvatar = $this->offer->user->company->video_avatar ?? false;
            $avatarExpireDate = $this->offer->user->company->video_avatar_expire_date;
            $isVideoAvatarAlreadyPaid = $avatarExpireDate != null && $avatarExpireDate > Carbon::now();
            if ($this->offer->user->hasRole(ACL::ROLE_COMPANY) && $videoAvatar && !$isVideoAvatarAlreadyPaid) {
                $this->details['company_video_avatar'] = [
                    'name' => 'Wideo avatar',
                    'value' => $videoPrice->value,
                    'id' => $videoPrice->id,
                ];
                $this->billAmount += $videoPrice->value;
            }
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
