<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type'
    ];

    /**
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getAvatarAttribute(): ?Avatar
    {
        $companyOwner = User::where('own_company_id', $this->id)->first();
        return $companyOwner->avatar;
    }

    public function getVideoAvatarAttribute(): ?Avatar
    {
        $companyOwner = User::where('own_company_id', $this->id)->first();
        return $companyOwner->videoAvatar;
    }

}
