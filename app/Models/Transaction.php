<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webpatser\Uuid\Uuid;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'name', 'code', 'status', 'total', 'address', 'line_items', 'visible', 'user_id', 'offer_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = ['line_items' => 'array', 'address' => 'array'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNumberAttribute()
    {
        return $this->created_at->format('Y') . '/A' . $this->id;
    }
}
