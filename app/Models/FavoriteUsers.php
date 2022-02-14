<?php

namespace App\Models;

use App\Enums\AttributeType;
use App\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FavoriteUsers extends Model
{
	protected $table = 'favorite_users';
	
    /** @var array */
    protected $fillable = [
        'allow_notifications',
        'fav_user_id',
        'user_id',
    ];

    /** @var string[]  */
    protected $casts = ['allow_notifications' => 'bool'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
