<?php

namespace Vasoft\Core\Settings\Entities\Fields;


use Vasoft\Core\Settings\Field;

/**
 * Вывод разделителя
 */
class SeparatorField extends Field
{
    private static int $index = 0;

    public function __construct()
    {
        parent::__construct(
            sprintf('SEP_%d', self::$index++),
            '',
            static fn() => null
        );
    }

    public function render(): string
    {
        return '<tr><td colspan="2"><hr></td></tr>';
    }

    public function renderInput(): string
    {
        return '';
    }
}