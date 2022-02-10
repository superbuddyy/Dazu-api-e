<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use SlugableTrait;
    use NodeTrait;

    /**
     * The attribute forming the slug.
     *
     * @var string
     */
    public const SLUG_BASE = 'name';

    /** @var string[]  */
    protected $fillable = ['is_active', 'name', 'parent_id', 'slug', 'description'];

    /** @var string[]  */
    protected $casts = ['is_active' => 'boolean', 'offer_types' => 'array'];

    /**
     * @return HasMany
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }
}
