<?php

declare(strict_types=1);

namespace App\Managers;

use App\Enums\AvatarType;
use App\Enums\CompanyType;
use App\Enums\OfferStatus;
use App\Enums\AttributeType;
use App\Events\Offer\OfferActivated;
use App\Laravue\Acl;
use App\Laravue\JsonResponse;
use App\Models\Category;
use App\Models\Offer;
use App\Models\PreviewOffer;
use App\Models\Attribute;
use App\Models\PreviewPhoto as Photo;
use App\Models\PreviewAvatar as Avatar;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreviewOfferManager
{
    /**
     * @param string|null $status
     * @param bool $randomOrder
     * @param bool $promoted
     * @return mixed
     */
    public function getList(?string $status = OfferStatus::ACTIVE, bool $randomOrder = false, bool $promoted = false)
    {
        $query = PreviewOffer::where('expire_time', '>', Carbon::now())
            ->where(function ($query) {
                $query->where('visible_from_date', '<', Carbon::now())
                    ->orWhere('visible_from_date', null);
            });


        if ($status != null) {
            $query->where('status', $status);
        }

        if ($promoted) {
            $query->whereHas('subscriptions', function ($query) {
               $query->where('subscriptions.id', '!=', 1);
               $query->where('end_date', '>', Carbon::now());
            });
        }

        if ($randomOrder) {
            $query->inRandomOrder();
        }

        return $query->paginate(config('dazu.pagination.per_page'));
    }

    public function getMyList(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->getRoleName() === Acl::ROLE_COMPANY && $user->company) {
            $companyMembers = User::where('company_id', $user->company_id)->pluck('id')->all();
            $offers = PreviewOffer::whereIn('user_id', $companyMembers);
        } else {
            $offers = $user->offers();
        }

        return $offers->orderBy('expire_time', 'DESC')
            ->orderBy('status', 'ASC')
            ->paginate(10);
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
        string $userId = null,
        ?bool $hasRaiseOne = false,
        ?bool $hasRaiseThree = false,
        ?bool $hasRaiseTen = false,
        ?bool $isUrgent = false,
        ?bool $isBargain = false
    ): PreviewOffer
    {
        $userId = $userId ?: Auth::id();
        $offer = [
            'title' => $name,
            // 'description' => strip_tags($description),
            'description' => $description,
            'price' => $price,
            'status' => OfferStatus::IN_ACTIVE, // Default
            'category_id' => Category::where('slug', $categorySlug)->firstOrFail()->id,
            'lat' => $lat,
            'lon' => $lon,
            'location_name' => $locationName,
            'links' => $links,
            'user_id' => $userId ?? null,
            'has_raise_one' => $hasRaiseOne,
            'has_raise_three' => $hasRaiseThree,
            'has_raise_ten' => $hasRaiseTen,
            'is_urgent' => $isUrgent,
            'is_bargain' => $isBargain,
        ];
        if ($visible_from_date !== 'null' && $visible_from_date !== null) {
            $offer['visible_from_date'] = $visible_from_date;
        }

        $offer = PreviewOffer::create($offer);

        $this->storeAttributes($offer, $attributes);

        return $offer;
    }

    public function storeImage($file, PreviewOffer $model, $position = 1, $type = 'photo')
    {
        $imageService = resolve(ImageService::class);
        $photo = Photo::make([
            'file' => $imageService->store($file, Photo::class),
            'description' => '',
            'img_type' => $type,
            'position' => $position
        ]);

        $model->photos()->save($photo);

        return $photo ? $photo : null;
    }
    public function removeAvatar(int $photoId, string $filePath)
    {
        $photo = Photo::find($photoId);
        if ($photo) {
            $photo->delete();
        }

        $imageService = resolve(ImageService::class);
        return $imageService->delete($filePath);
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
    public function storeAvatar(PreviewOffer $user, $file, $isActive = false)
    {
        $this->removeAvatars($user);
        $imageService = resolve(ImageService::class);
        $photo = Avatar::make([
            'file' => $imageService->store($file, Avatar::class),
            'expire_date' => Carbon::now()->addDays(30),
            'is_active' => $isActive,
            'type' => AvatarType::PHOTO
        ]);

        return $user->avatars()->save($photo);
    }

    public function storeVideoAvatar(PreviewOffer $user, string $url, $isActive = false)
    {
        $this->removeAvatars($user, AvatarType::VIDEO_URL);
        $videoAvatar = Avatar::make([
            'file' => $url,
            'expire_date' => Carbon::now()->addDays(30),
            'is_active' => $isActive,
            'type' => AvatarType::VIDEO_URL
        ]);

        return $user->avatars()->save($videoAvatar);
    }

    public function removeAvatars(PreviewOffer $user, string $avatarType = AvatarType::PHOTO)
    {
        $avatars = $user->avatars()->where('type', $avatarType);
        if ($avatarType === AvatarType::PHOTO) {
            $imageService = resolve(ImageService::class);
            foreach ($avatars->get() as $avatar) {
                $imageService->delete($avatar->file['path_name']);
            }
        }

        return $avatars->delete();
    }
    public function deletePreviewData($p_offer) {
        DB::table('attribute_value')->where('preview_offer_id',$p_offer->id)->delete();
        DB::table('preview_photos')->where('preview_offer_id',$p_offer->id)->delete();
        DB::table('preview_avatars')->where('preview_offer_id',$p_offer->id)->delete();
        DB::table('preview_offers')->where('id',$p_offer->id)->delete();
        return true;
    }
    public function update(
        PreviewOffer $offer,
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
    ): ?PreviewOffer
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
        return PreviewOffer::whereIn('id', $offersIds)->update(['status' => $status]);
    }

    public function changeStatus(Offer $offer, string $status, string $note = null, bool $fromPayment = false): Offer
    {
        if ($fromPayment) {
            $total_raise = 0;
            if ($offer->has_raise_one) {
                $total_raise = $total_raise + 1;
            }
            if ($offer->has_raise_three) {
                $total_raise = $total_raise + 3;
            }
            if ($offer->has_raise_ten) {
                $total_raise = $total_raise + 10;
            }
            if ($status === OfferStatus::ACTIVE || $status === OfferStatus::IN_ACTIVE) {
                $offer->total_raises = $total_raise;
            }
        }
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

    public function changeSubscription(Offer $offer, Subscription $subscription): Offer
    {
        $offer->subscriptions()->detach();
        $offer->subscriptions()
            ->attach(
                $subscription->id,
                ['end_date' => Carbon::now()->addHours($subscription->duration)]
            );

        return $offer;
    }

    /**
     * @param Offer $offer
     * @return mixed
     */
    public function getSimilar(Offer $offer)
    {
        $query = PreviewOffer::where('expire_time', '>', Carbon::now())
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
        $offer = PreviewOffer::findOrFail($cachedInfo['offer_id']);
        if ($cachedInfo['transaction_id']) {
            $transaction = Transaction::findOrFail($cachedInfo['transaction_id']);
            foreach ($transaction->line_items as $item) {
                // Avatar
                if (isset($item['id']) && $item['id'] === 4) {
                    $user = User::findOrFail($cachedInfo['user_id']);
                    $user->avatar->update(['is_active' => true, 'expire_date' => Carbon::now()->addDays(30)]);
                }

                // Video Avatar
                if (isset($item['id']) && $item['id'] === 7) {
                    $user = User::findOrFail($cachedInfo['user_id']);
                    $user->avatar->update(['is_active' => true, 'expire_date' => Carbon::now()->addDays(30)]);
                }
            }
        }
        if ($offer->visible_from_date) {
            $this->changeStatus($offer, OfferStatus::ACTIVE, null, true);
        } else {
            $this->changeStatus($offer, OfferStatus::IN_ACTIVE,null, true);
        }
    }

    /**
     * @param array $cachedInfo
     */
    public function handleRefreshPaymentCallback(array $cachedInfo)
    {
        $offer = PreviewOffer::findOrFail($cachedInfo['offer_id']);
        $this->refresh($offer);
    }

    /**
     * @param array $cachedInfo
     */
    public function handleRaisePaymentCallback(array $cachedInfo)
    {
        $offer = PreviewOffer::findOrFail($cachedInfo['offer_id']);
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

    public function reduceRaise(Offer $offer): bool {
        $raises = $offer->total_raises - 1;
        if ($raises < 0) {
            $raises = 0;
        }
        $offer->total_raises = $raises;
        return $offer->save();   
    }

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
    protected function storeAttributes(PreviewOffer $offer, array $attributes): PreviewOffer
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
