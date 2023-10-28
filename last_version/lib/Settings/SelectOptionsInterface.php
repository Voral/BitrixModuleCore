<?php

namespace Vasoft\Core\Settings;

interface SelectOptionsInterface
{
    /** Возвращает список для Select значение в качестве ключа - Описание */
    public static function getList(): array;

    /**
     * Проверка допустимости значения
     * @param string|int $value
     * @return bool
     */
    public static function valid(string|int $value): bool;

    /**
     * Возвращает описание опции
     * @return string
     */
    public function caption(): string;

}