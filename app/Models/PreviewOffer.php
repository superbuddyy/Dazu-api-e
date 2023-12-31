<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Billings\OfferBillingService;
use App\Traits\AuditFieldsTrait;
use App\Traits\SlugableTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class PreviewOffer extends Model
{
    use SoftDeletes;
    use AuditFieldsTrait;
    use SlugableTrait;
    protected $table = 'preview_offers';
    /**
     * The attribute forming the slug.
     *
     * @var string
     */
    public const SLUG_BASE = 'title';

    /**
     *  List of paid properties
     */
    public const LINK_1 = 'link_1';
    public const LINK_2 = 'link_2';
    public const LINK_3 = 'link_3';
    public const URGENT = 'urgent';
    public const PHOTO = 'photo';
    public const VISIBLE_FROM_DATE = 'visible_from_date';

    /** @var string  */
    protected $primaryKey = 'id';

    /** @var bool */
    public $incrementing = false;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'old_price',
        'status',
        'lat',
        'lon',
        'location_name',
        'category_id',
        'user_id',
        'links',
        'refresh_count',
        'visible_from_date',
        'note',
        'expire_time',
        'has_raise_one',
        'has_raise_three',
        'has_raise_ten',
        'is_bargain',
        'is_urgent'
    ];

    protected $dates = ['expire_time', 'created_at', 'updated_at'];

    /** @var string[]  */
    protected $casts = ['links' => 'json', 'is_bargain' => 'boolean', 'is_urgent' => 'boolean', 'has_raise_one', 'has_raise_three', 'has_raise_ten'];

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model): void {
            $model->{$model->getKeyName()} = Uuid::generate()->string;
        });
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany
     */
    public function photos(): HasMany
    {
        return $this->hasMany(PreviewPhoto::class);
    }
    public function avatars(): HasMany
    {
        return $this->hasMany(PreviewAvatar::class);
    }
    /**
     * @return BelongsToMany
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(
            Attribute::class,
            'attribute_value',
            'preview_offer_id',
            'attribute_id'
        )->withPivot('value');
    }

    /**
     * @return BelongsToMany
     */
    // public function subscriptions(): BelongsToMany
    // {
    //     return $this->belongsToMany(
    //         Subscription::class,
    //         'offer_subscriptions',
    //         'preview_offer_id',
    //         'subscription_id'
    //     )->withPivot('end_date');
    // }

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return Model|HasMany|object|null
     */
    public function getMainPhotoAttribute()
    {
        $photo = $this->photos()->where('position', 1)->first();
        return $photo ? $photo->makeHidden(['id', 'position']) : null;
    }

    /**
     * @return Subscription|null
     */
    public function getActiveSubscriptionAttribute(): ?Subscription
    {
        $subscription = $this->subscriptions->where('pivot.end_date', '>', Carbon::now())
            ->first();


        if ($subscription === null) {
            return Subscription::where('id', 1)->first();
        }

        return $subscription;
    }

    /**
     * @return bool
     */
    public function getIsExpiredAttribute()
    {
        return $this->expire_time < Carbon::now();
    }

    /**
     * @param string $userId
     * @return Favorite|null
     */
    public function getAvatarAttribute(): ?Avatar
    {
        return $this->avatars->where('type', AvatarType::PHOTO)->first() ?? null;
    }

    /**
     * @return Avatar|null
     */
    public function getVideoAvatarAttribute(): ?Avatar
    {
        return $this->avatars->where('type', AvatarType::VIDEO_URL)->first() ?? null;
                
    }

    public function getAvatar(string $type = AvatarType::PHOTO): ?Avatar
    {
        return $this->avatars->where('type', $type)->first() ?? null;
    }

    public function calculateBill()
    {
        return (new OfferBillingService($this))->calculateBill();
    }
}
