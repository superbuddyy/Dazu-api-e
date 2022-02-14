<?php

declare(strict_types=1);

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait AuditFieldsTrait
{
    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model): void {
            if ($user = Auth::user()) {
                $model->created_by = $user->id;
            }
        });
        self::updating(function ($model): void {
            $model->updated_at = Carbon::now();
            if ($user = Auth::user()) {
                $model->updated_by = $user->id;
            }
        });
    }
}
