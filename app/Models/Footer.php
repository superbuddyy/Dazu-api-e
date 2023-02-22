<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Footer extends Model
{
    use SlugableTrait;
    use SoftDeletes;

    /**
     * Storage images path
     */
    public const IMAGES_PATH = 'posts';

    /**
     * The attribute forming the slug.
     *
     * @var string
     */
    public const SLUG_BASE = 'title';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'content', 'status', 'main_photo', 'user_id'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return string
     */
    public function getMainPhotoUrlAttribute(): string
    {
        return config('app.url') . '/storage/' . self::IMAGES_PATH . '/' . $this->main_photo;
    }
}
