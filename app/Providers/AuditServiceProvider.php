<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
    }

    public function register(): void
    {
        Blueprint::macro('auditable', function (): void {
            $this->datetime('created_at')
                ->useCurrent();
            $this->string('created_by')
                ->nullable();
            $this->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $this->datetime('updated_at')
                ->nullable();
            $this->string('updated_by')
                ->nullable();
            $this->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        Blueprint::macro('dropAuditable', function (): void {
            $this->dropColumn(
                [
                    'created_by',
                    'created_at',
                    'updated_by',
                    'updated_at',
                ]
            );
        });
    }
}
