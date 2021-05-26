<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AttributeType;
use App\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use SlugableTrait;

    /**
     * The attribute forming the slug.
     *
     * @var string
     */
    public const SLUG_BASE = 'name';

    /** @var array */
    protected $fillable = ['description', 'id', 'name', 'slug', 'type', 'unit'];

    /** @var array */
    protected $hidden = ['created_at', 'updated_at', 'value'];

    /** @var string[]  */
    protected $casts = ['offer_types' => 'array'];

    /** @var array */
    protected $appends = ['value', 'result'];

    /**
     * The options that belong to the attribute.
     */
    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }

    /**
     * The products that own the attribute.
     */
    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(
            Offer::class,
            'attribute_value',
            'attribute_id',
            'offer_id'
        )->withPivot(['value']);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the value for the attribute (pivot).
     *
     * @return boolean|integer|float|string|null
     */
    public function getValueAttribute()
    {
        return $this->pivot ? $this->pivot->value : null;
    }

    /**
     * Get the value for the attribute (pivot).
     *
     * @return boolean|integer|float|string|null
     */
    public function getResultAttribute()
    {
        $options = Attribute::where('id', $this->pivot->attribute_id)->with('options')->first()->options;
        if ($options->count() > 0) {
            return $options->filter(function ($option) {
               return $option->slug === $this->pivot->value;
            })->first()['name'] ?? null;
        }

        return $this->pivot ? $this->pivot->value : null;
    }
}
