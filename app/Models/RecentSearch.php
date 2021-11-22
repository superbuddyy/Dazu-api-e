<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class RecentSearch extends Model
{
    /** @var array */
    protected $fillable = [
        'display_name',
        'lat',
        'lon',
        'offer_id',
        'user_id',
    ];
    protected $hidden = ['created_at','updated_at'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
