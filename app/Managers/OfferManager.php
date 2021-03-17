<?php

declare(strict_types=1);

namespace App\Managers;

use App\Enums\OfferStatus;
use App\Enums\AttributeType;
use App\Enums\OfferType;
use App\Events\Offer\OfferActivated;
use App\Laravue\Acl;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Photo;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OfferManager
{
    /**
     * @param string|null $status
     * @param bool $randomOrder
     * @param bool $promoted
     * @return mixed
     */
    public function getList(?string $status = OfferStatus::ACTIVE, bool $randomOrder = false, bool $promoted = false)
    {
        $query = Offer::where('expire_time', '>', Carbon::now())
            ->where(function ($query) {
                $query->where('visible_from_date', '<', Carbon::now())
                    ->orWhere('visible_from_date', null);
            });


        if ($status != null) {
            $query->where('status', $status);
        }

        if ($promoted) {
            $query->whereHas('subscriptions', function ($query) {
               $query->whereIn('subscriptions.id', [2,3,4]);
               $query->where('end_date', '>', Carbon::now());
            });
        }

        if ($randomOrder) {
            $query->inRandomOrder();
        }

        return $query->paginate(config('dazu.pagination.per_page'));
    }

    /**
     * @param string $name
     * @param string $description
     * @param int $price
     * @param string $categorySlug
     * @param array $attributes
     * @param string $lat
     * @param string $lon
     * @param string $locationName
     * @param array $links
     * @param string|null $visible_from_date
     * @param string|null $userId
     * @return Offer
     */
    public function store(
        string $name,
        string $description,
        int $price,
        string $categorySlug,
        array $attributes,
        string $lat,
        string $lon,
        string $locationName,
        array $links = [],
        ?string $visible_from_date = null,
        string $userId = null
    ): Offer
    {
        $userId = $userId ?: Auth::id();
        $offer = [
            'title' => $name,
            'description' => strip_tags($description),
            'price' => $price,
            'status' => OfferStatus::IN_ACTIVE, // Default
            'category_id' => Category::where('slug', $categorySlug)->firstOrFail()->id,
            'lat' => $lat,
            'lon' => $lon,
            'location_name' => $locationName,
            'links' => $links,
            'user_id' => $userId,
        ];
        if ($visible_from_date !== 'null' && $visible_from_date !== null) {
            $offer['visible_from_date'] = $visible_from_date;
        }

        $offer = Offer::create($offer);

        $this->storeAttributes($offer, $attributes);

        return $offer;
    }

    public function storeImage($file, Offer $model, $position = 1)
    {
        $imageService = resolve(ImageService::class);
        $photo = Photo::make([
            'file' => $imageService->store($file, Photo::class),
            'description' => '',
            'position' => $position
        ]);

        $model->photos()->save($photo);

        return $photo ? $photo : null;
    }

    public function removeImage(int $photoId, string $filePath)
    {
        $photo = Photo::find($photoId);
        if ($photo) {
            $photo->delete();
        }

        $imageService = resolve(ImageService::class);
        return $imageService->delete($filePath);
    }

    public function update(
        Offer $offer,
        string $name,
        string $description,
        int $price,
        string $categorySlug,
        array $attributes,
        string $lat,
        string $lon,
        string $locationName,
        array $links = [],
        string $visible_from_date = null,
        string $status = OfferStatus::IN_ACTIVE
    ): ?Offer
    {
        $update = $offer->update([
            'title' => strip_tags($name,'<b><p><i><ul><li><ol>'),
            'description' => strip_tags($description,'<b><p><i><ul><li><ol>'),
            'price' => $price,
            'category_id' => Category::where('slug', $categorySlug)->firstOrFail()->id,
            'lat' => $lat,
            'lon' => $lon,
            'location_name' => $locationName,
            'links' => $links,
            'visible_from_date' => $visible_from_date === 'null' ? null : $visible_from_date,
            'status' => $status,
        ]);

        if (!$update) {
            return null;
        }

        $offer->attributes()->detach();
        $this->storeAttributes($offer, $attributes);

        return $offer->fresh();
    }

    public function changeStatusMultiple(array $offersIds, string $status): int
    {
        return Offer::whereIn('id', $offersIds)->update(['status' => $status]);
    }

    public function changeStatus(Offer $offer, string $status, string $note = null): Offer
    {
        if ($note) {
            $offer->note = $note;
        }
        $offer->status = $status;
        $offer->save();
        if ($status === OfferStatus::ACTIVE) {
            event(new OfferActivated($offer));
        }

        return $offer;
    }

    /**
     * @param Offer $offer
     * @return mixed
     */
    public function getSimilar(Offer $offer)
    {
        $query = Offer::where('expire_time', '>', Carbon::now())
            ->where('status', OfferStatus::ACTIVE)
            ->whereHas('category', function($query) use ($offer) {
                return $query->where('_lft', '>=', $offer->category->_lft)
                    ->where('_rgt', '<=', $offer->category->_rgt);
            })
            ->inRandomOrder();

        return $query->paginate(config('dazu.pagination.per_page'));
    }

    /**
     * @param array $cachedInfo
     */
    public function handlePaymentCallback(array $cachedInfo): void
    {
        $offer = Offer::findOrFail($cachedInfo['offer_id']);
        if ($cachedInfo['transaction_id']) {
            $transaction = Transaction::findOrFail($cachedInfo['transaction_id']);
            foreach ($transaction->line_items as $item) {
                // Avatar
                if (isset($item['id']) && $item['id'] === 4) {
                    $user = User::findOrFail($cachedInfo['user_id']);
                    if ($user->hasRole(Acl::ROLE_COMPANY)) {
                        $company = $offer->user->company;
                        $company->avatar_expire_date = Carbon::now()->addDays(30);
                        $company->save();
                    } else {
                        $user->avatar->update(['is_active' => true, 'expire_date' => Carbon::now()->addDays(30)]);
                    }
                }

                // Video Avatar
                if (isset($item['id']) && $item['id'] === 7) {
                    $user = User::findOrFail($cachedInfo['user_id']);
                    if ($user->hasRole(Acl::ROLE_COMPANY)) {
                        $company = $offer->user->company;
                        $company->video_avatar_expire_date = Carbon::now()->addDays(30);
                        $company->save();
                    }
                }
            }
        }
        $this->changeStatus($offer, OfferStatus::IN_ACTIVE);
    }

    /**
     * @param array $cachedInfo
     */
    public function handleRefreshPaymentCallback(array $cachedInfo)
    {
        $offer = Offer::findOrFail($cachedInfo['offer_id']);
        $this->refresh($offer);
    }

    /**
     * @param array $cachedInfo
     */
    public function handleRaisePaymentCallback(array $cachedInfo)
    {
        $offer = Offer::findOrFail($cachedInfo['offer_id']);
        $this->raise($offer);
    }

    /**
     * @param Offer $offer
     * @param int $duration
     * @return bool
     */
    public function refresh(Offer $offer, int $duration = 168): bool
    {
        $offer->expire_time = Carbon::now()->addHours($duration);
        $offer->refresh_count++;
        return $offer->save();
    }

    /**
     * @param Offer $offer
     * @return bool
     */
    public function raise(Offer $offer): bool
    {
        $offer->raise_at = Carbon::now();
        $offer->raise_count++;
        return $offer->save();
    }

    /**
     * @param Offer $offer
     * @param array $attributes
     * @return Offer
     */
    protected function storeAttributes(Offer $offer, array $attributes): Offer
    {
        $attributesKeys = array_keys($attributes);
        $attributesModels = Attribute::whereIn('id', $attributesKeys)->get();

        foreach ($attributesModels as $attribute) {
            $key = array_search($attribute->id, $attributesKeys);
            $value = $attributes[$attributesKeys[$key]];

            if (empty($value)) {
                continue;
            }

            if ($attribute->type === AttributeType::CHOICE || $attribute->type === AttributeType::MULTI_CHOICE) {
                $options = $attribute->options->whereIn('slug', explode(',', $value));
                foreach ($options as $option) {
                    $offer->attributes()->attach($attribute->id, ['value' => $option->slug]);
                }
            } else {
                $offer->attributes()->attach($attribute->id, ['value' => $value]);
            }
        }

        return $offer;
    }
}
