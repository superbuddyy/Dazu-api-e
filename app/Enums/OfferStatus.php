<?php

declare(strict_types=1);

namespace App\Enums;

final class OfferStatus extends BaseEnum
{
    public const ACTIVE = 'active';
    public const IN_ACTIVE = 'in_active';
    public const IN_ACTIVE_BY_USER = 'in_active_by_user';
    public const PENDING = 'pending';
    public const REJECTED = 'rejected';
}
