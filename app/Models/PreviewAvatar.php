<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AvatarType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PreviewAvatar extends Model
{
    public const IMAGES_PATH = 'users';
    protected $table = 'preview_avatars';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file', 'expire_date', 'type', 'is_active', 'preview_offer_id'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(PreivewOffer::class);
    }

    /**
     * @return array|null
     */
    public function getFileAttribute(): ?array
    {
        if ($this->attributes['type'] === AvatarType::VIDEO_URL) {
            return [
                'original_name' => null,
                'path_name' => null,
                'url' => $this->attributes['file'],
            ];
        }

        $pathname = self::IMAGES_PATH . (!Str::endsWith(self::IMAGES_PATH, '/') ? '/' : '') . $this->attributes['file'];

        return [
            'original_name' => $this->attributes['file'],
            'path_name' => $pathname,
            'url' => config('app.url') . '/storage/' . $pathname,
        ];
    }
}
