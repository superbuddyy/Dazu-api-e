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

class Offer extends Model
{
    use SoftDeletes;
    use AuditFieldsTrait;
    use SlugableTrait;

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
        'expire_time'
    ];

    protected $dates = ['expire_time', 'created_at', 'updated_at'];

    /** @var string[]  */
    protected $casts = ['links' => 'json'];

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
        return $this->hasMany(Photo::class);
    }

    /**
     * @return BelongsToMany
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(
            Attribute::class,
            'attribute_value',
            'offer_id',
            'attribute_id'
        )->withPivot('value');
    }

    /**
     * @return BelongsToMany
     */
    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(
            Subscription::class,
            'offer_subscriptions',
            'offer_id',
            'subscription_id'
        )->withPivot('end_date', 'urgent', 'bargain', 'raises');
    }

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
    public function getFavorite(string $userId): ?Favorite
    {
        return Favorite::where(['offer_id' => $this->id, 'user_id' => $userId])->first();
    }

    public function calculateBill()
    {
        return (new OfferBillingService($this))->calculateBill();
    }
}
