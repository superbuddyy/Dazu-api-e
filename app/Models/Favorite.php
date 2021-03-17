<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AttributeType;
use App\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Favorite extends Model
{
    /** @var array */
    protected $fillable = [
        'allow_notifications',
        'offer_id',
        'user_id',
    ];

    /** @var string[]  */
    protected $casts = ['allow_notifications' => 'bool'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
