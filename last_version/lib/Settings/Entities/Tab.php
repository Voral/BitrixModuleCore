<?php

namespace Vasoft\Core\Settings\Entities;
use Vasoft\Core\Settings\Field;

final class Tab
{
    public readonly string $title;

    /**
     * @param string $divId Идентификатор блока
     * @param string $name Наименование
     * @param Field[] $fields Массив полей
     * @param string $title Полное наименование (если не указано равно Наименованию)
     * @param string $onSelectJs Строка JS, обработчик события активации таба
     */

    public function __construct(
        public readonly string $divId,
        public readonly string $name,
        public readonly array  $fields,
        string                 $title = '',
        public readonly string $onSelectJs = '',
    )
    {
        $this->title = $title === '' ? $this->name : $title;
    }

    public function map(): array
    {
        $result = [
            'DIV' => $this->divId,
            'TAB' => $this->name,
            'TITLE' => $this->title,
        ];
        if ($this->onSelectJs !== '') {
            $result['ONSELECT'] = $this->onSelectJs;
        }
        return $result;
    }
}