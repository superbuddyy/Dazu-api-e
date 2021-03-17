<?php

declare(strict_types=1);

namespace App\Enums;

final class TransactionStatus extends BaseEnum
{
    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const CANCELED = 'canceled';
}
