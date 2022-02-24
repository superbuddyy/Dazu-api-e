<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AttributeType;
use App\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecentSearchFilter extends Model
{
    protected $table = 'recent_search_filters';
    /** @var array */
    protected $fillable = ['user_id', 'filters', 'encoded_txt' ,'notification', 'period', 'next_notification_date'];

    /** @var string[]  */
    protected $casts = ['filters' => 'array', 'notification' => 'bool'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
