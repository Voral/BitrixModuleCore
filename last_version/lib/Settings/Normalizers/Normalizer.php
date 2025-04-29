<?php

declare(strict_types=1);
/** @noinspection PhpUnused */

namespace Vasoft\Core\Settings\Normalizers;

class Normalizer
{
    public static function normalizeInt(string $value): int
    {
        return (int) $value;
    }

    public static function normalizeNotZeroInt(string $value): int|string
    {
        $valueNormalized = (int) $value;

        return 0 === $valueNormalized ? '' : $valueNormalized;
    }

    public static function normalizeBoolean(string $value): string
    {
        return 'Y' === $value ? 'Y' : 'N';
    }

    public static function normalizeString(string $value): string
    {
        return trim($value);
    }

    /**
     * Нормализация строки разделяемой запятыми.
     */
    public static function normalizeCommaSeparatedString(string $value): string
    {
        $value = preg_replace('# ?, ?#', ',', $value);

        return trim($value);
    }

    /**
     * Нормализация строки содержащей целые числа разделяемые запятыми.
     */
    public static function normalizeCommaSeparatedInteger(string $value): string
    {
        $values = explode(',', static::normalizeCommaSeparatedString($value));

        return implode(',', array_map('intval', $values));
    }
}
