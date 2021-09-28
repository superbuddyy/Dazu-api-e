<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Subscription extends Model
{
    // Ids
    public const FREE = 1;
    public const STANDARD = 2;
    public const SILVER = 3;
    public const GOLD = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'number_of_refreshes',
        'refresh_price',
        'number_of_raises',
        'raise_price',
        'raise_price_three',
        'raise_price_ten',
        'bargain_price',
        'urgent_price',
        'config',
        'featured_on_homepage',
        'featured_on_search_results_and_categories'
    ];

    /** @var string[]  */
    protected $casts = ['config' => 'json', 'featured_on_homepage' => 'boolean', 'featured_on_search_results_and_categories' => 'boolean'];

    /**
     * The products that own the attribute.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            Offer::class,
            'offer_subscriptions',
            'subscription_id',
            'offer_id'
        )->withPivot('end_date', 'urgent', 'bargain', 'raises');
    }

    /**
     * Get the end_date for the subscription (pivot).
     *
     * @return boolean|integer|float|string|null
     */
    public function getEndDateAttribute()
    {
        return $this->pivot ? $this->pivot->end_date : null;
    }
}
