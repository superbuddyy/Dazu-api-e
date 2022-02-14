<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeOption extends Model
{
    use SlugableTrait;

    /**
     * The attribute forming the slug.
     *
     * @var string
     */
    public const SLUG_BASE = 'name';

    /** @var array */
    protected $fillable = ['id', 'name', 'slug'];

    /** @var array */
    protected $hidden = ['attribute_id', 'created_at', 'updated_at'];

    /** @var string[]  */
    protected $casts = ['offer_types' => 'array'];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
