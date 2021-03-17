<?php

declare(strict_types=1);

namespace App\Enums;

final class AttributeType extends BaseEnum
{
    public const BOOLEAN = 'boolean';
    public const INTEGER = 'integer';
    public const NUMERIC = 'numeric';
    public const CHOICE = 'choice';
    public const MULTI_CHOICE = 'multi_choice';
    public const DATE = 'date';
    public const DATETIME = 'datetime';
    public const STRING = 'string';
}
