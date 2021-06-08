<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\AuditFieldsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Photo extends Model
{
    public const IMAGES_PATH = 'offers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'position', 'path', 'file', 'offer_id'
    ];

    /**
     * @return BelongsTo
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function getFileAttribute(): ?array
    {
        $pathname = self::IMAGES_PATH . (!Str::endsWith(self::IMAGES_PATH, '/') ? '/' : '') . $this->attributes['file'];

        return [
            'original_name' => $this->attributes['file'],
            'path_name' => $pathname,
            'url' => config('app.url') . '/storage/' . $pathname,
        ];
    }
}
