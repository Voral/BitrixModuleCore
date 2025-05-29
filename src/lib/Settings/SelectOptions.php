<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings;

/**
 * Трейт для использования в перечислениях для реализации опций списков выбора
 * Перечисление при этом должно реализовывать интерфейс SelectOptionsInterface.
 *
 * @see SelectOptionsInterface
 */
trait SelectOptions
{
    public static function getList(): array
    {
        $cases = self::cases();
        $result = [];
        foreach ($cases as $case) {
            $result[$case->value] = $case->caption();
        }

        return $result;
    }

    /**
     * Метода для проверки валидности значения, например, в нормализатре.
     */
    public static function valid(int|string $value): bool
    {
        return (bool) self::tryFrom($value);
    }
}
