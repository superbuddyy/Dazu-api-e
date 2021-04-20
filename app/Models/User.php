<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AvatarType;
use App\Laravue\Models\Role;
use App\Laravue\Models\User as LaravueUser;
use App\Traits\AuditFieldsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;

class User extends LaravueUser
{
    use AuditFieldsTrait;
    use SoftDeletes;
    // Check App\Laravue\Models\User

    /** @var bool */
    public $incrementing = false;

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model): void {
            $model->{$model->getKeyName()} = Uuid::generate()->string;
            if ($user = Auth::user()) {
                $model->created_by = $user->getId();
            }
        });
        self::updating(function ($model): void {
            $model->updated_at = Carbon::now();
            if ($user = Auth::user()) {
                $model->updated_by = $user->getId();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'verification_token', 'company_id', 'own_company_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'verification_token'
    ];

    /**
     * @return HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return HasMany
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * @return HasMany
     */
    public function avatars(): HasMany
    {
        return $this->hasMany(Avatar::class);
    }

    /**
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @return HasOne
     */
    public function getCreatedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * @return HasOne
     */
    public function getUpdatedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function getRoleName()
    {
        $roleName = '';
        foreach ($this->roles as $role) {
            $roleName = Role::where('id', $role->pivot->role_id)->first();
        }

        return $roleName->name ?? null;
    }

    /**
     * @return Model|HasMany|object|null
     */
    public function getAvatarAttribute(): ?Avatar
    {
        return $this->avatars->where('expire_date', '>', Carbon::now())
                ->where('is_active', true)
                ->where('type', AvatarType::PHOTO)->first() ?? null;
    }

    /**
     * @return Avatar|null
     */
    public function getVideoAvatarAttribute(): ?Avatar
    {
        return $this->avatars->where('expire_date', '>', Carbon::now())
                ->where('is_active', true)
                ->where('type', AvatarType::VIDEO_URL)->first() ?? null;
    }

    public function getAvatar(string $type = AvatarType::PHOTO): ?Avatar
    {
        return $this->avatars->where('type', $type)->first() ?? null;
    }
}
