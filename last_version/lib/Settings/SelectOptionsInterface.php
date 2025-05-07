<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings;

interface SelectOptionsInterface
{
    /**
     * Возвращает список для Select значение в качестве ключа - Описание.
     * @return array<string, string>
     */
    public static function getList(): array;

    /**
     * Проверка допустимости значения.
     */
    public static function valid(int|string $value): bool;

    /**
     * Возвращает описание опции.
     */
    public function caption(): string;
}
