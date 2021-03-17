<?php

declare(strict_types=1);

namespace App\Enums;

use ReflectionClass;

class BaseEnum
{
    /**
     * Constants cache
     *
     * @var array
     */
    private static $constCacheArray = [];

    public static function getLowerCaseKeys(): array
    {
        return \collect(self::getKeys())->map(function ($key) {
            return \strtolower($key);
        })->toArray();
    }

    /**
     * @param int|string $value
     * @return string
     */
    public static function getLowerCase($value): string
    {
        return \strtolower(self::getKey($value));
    }

    public static function getKeyFromDbValue(string $key): int
    {
        return self::getValue(\strtoupper($key));
    }

    /**
     * @param int|string $value
     * @return string
     */
    public static function getKey($value): string
    {
        return \array_search($value, self::getConstants(), true) ?: '';
    }

    public static function getKeys(): array
    {
        return \array_keys(self::getConstants());
    }

    /**
     * @return int|mixed|string
     */
    public static function getValue(string $key)
    {
        return self::getConstants()[$key];
    }

    public static function getValues(): array
    {
        return \array_values(self::getConstants());
    }

    private static function getConstants(): array
    {
        $calledClass = \get_called_class();

        if (!\array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }
}
