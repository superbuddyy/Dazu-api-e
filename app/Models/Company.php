<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use SoftDeletes;

    /** @var string  */
    public const IMAGES_PATH = 'company';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'avatar', 'avatar_expire_date', 'video_avatar', 'video_avatar_expire_date', 'type'
    ];

    /**
     * @return HasMany
     */
    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getAvatarAttribute(): ?array
    {
        if ($this->avatar_expire_date < Carbon::now()) {
            return null;
        }

        $pathname = self::IMAGES_PATH . (!Str::endsWith(self::IMAGES_PATH, '/') ? '/' : '')
            . $this->attributes['avatar'];

        return [
            'original_name' => $this->attributes['avatar'],
            'path_name' => $pathname,
            'url' => env('APP_URL') . '/storage/' . $pathname,
        ];
    }

    public function getVideoAvatarAttribute(): ?string
    {
        if ($this->video_avatar_expire_date < Carbon::now()) {
            return null;
        }

        return $this->video_avatar;
    }

}
