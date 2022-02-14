<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\AuditFieldsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use AuditFieldsTrait;

    /** @var bool */
    public $timestamps = false;

    /** @var string */
    protected $primaryKey = 'user_id';

    /** @var string */
    protected $table = 'user_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone', 'city', 'street', 'zip_code', 'country', 'nip', 'name', 'newsletter', 'default_avatar'
    ];

    /** @var string[]  */
    protected $casts = ['newsletter' => 'boolean'];

    /** @var string */
    protected $dateFormat = 'Y-m-d';

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
