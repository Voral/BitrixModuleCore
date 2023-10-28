<?php /** @noinspection PhpUnused */

namespace Vasoft\Core\Settings\Normalizers;

class Normalizer
{
    public static function normalizeInt(string $value): int
    {
        return (int)$value;
    }

    public static function normalizeNotZeroInt(string $value): string|int
    {
        $valueNormalized = (int)$value;
        return $valueNormalized === 0 ? '' : $valueNormalized;
    }

    public static function normalizeBoolean(string $value): string
    {
        return $value === 'Y' ? 'Y' : 'N';
    }

    public static function normalizeString(string $value): string
    {
        return trim($value);
    }
}