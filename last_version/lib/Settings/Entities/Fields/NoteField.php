<?php
namespace Vasoft\Core\Settings\Entities\Fields;

use Closure;
use Vasoft\Core\Settings\Field;

class NoteField extends Field
{
    private static int $index = 0;

    public function __construct(Closure $content)
    {
        parent::__construct(
            sprintf('NOTE_%d', self::$index++),
            '',
            $content
        );
    }

    public function render(): string
    {
        return '<tr><td colspan="2">' . BeginNote() . ($this->getter)() . EndNote() . '</td></tr>';
    }

    public function renderInput(): string
    {
        return '';
    }
}