<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Entities\Fields;

use Vasoft\Core\Settings\Field;

class HtmlField extends Field
{
    private static int $index = 0;

    public function __construct(\Closure $content)
    {
        parent::__construct(
            sprintf('HTML_%d', self::$index++),
            '',
            $content,
        );
    }

    public function render(): string
    {
        return '<tr><td colspan="2">' . ($this->getter)() . '</td></tr>';
    }

    public function renderInput(): string
    {
        return '';
    }
}
