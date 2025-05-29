<?php

declare(strict_types=1);
/** @noinspection PhpUnused */

namespace Vasoft\Core\Settings\Normalizers;

class Normalizer
{
    public static function normalizeInt(int|string $value): string
    {
        return (string) (int) $value;
    }

    public static function normalizeNotZeroInt(int|string $value): string
    {
        $valueNormalized = (int) $value;

        return 0 === $valueNormalized ? '' : (string) $valueNormalized;
    }

    public static function normalizeBoolean(string $value): string
    {
        $value = strtoupper(trim($value));

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
        $value = preg_replace('# *, *#', ',', $value);

        return trim($value, " \t\n\r\0\x0B,");
    }

    /**
     * Нормализация строки содержащей целые числа разделяемые запятыми.
     */
    public static function normalizeCommaSeparatedInteger(string $value): string
    {
        preg_match_all('/\b\d+\b/', $value, $matches);

        return implode(',', $matches[0]);
    }
}
