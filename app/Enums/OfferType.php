<?php

declare(strict_types=1);

namespace App\Enums;

final class OfferType extends BaseEnum
{
    public const SELL = 'sell';
    public const RENT = 'rent';
    public const EXCHANGE = 'exchange';
    public const SERVICES = 'services';
    public const FOR_FREE = 'for_free';
}
