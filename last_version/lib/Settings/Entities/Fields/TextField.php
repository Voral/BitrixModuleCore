<?php

namespace Vasoft\Core\Settings\Entities\Fields;


use Vasoft\Core\Settings\Field;

/**
 * Поле строки тег input с типом text
 */
class TextField extends Field
{
    protected int $width = 400;

    /**
     * Настройка ширины в пикселях
     * @param int $width
     * @return $this
     */
    public function configureWidth(int $width): static
    {
        $this->width = $width;
        return $this;
    }

    public function renderInput(): string
    {
        $value = htmlspecialchars(($this->getter)());
        return $this->renderInputAndValue($value);
    }

    protected function renderInputAndValue(string $value): string
    {
        return <<<HTML
<input type="text" maxlength="255" value="$value" name="$this->code" style="width:{$this->width}px;max-width:100%;"> 
HTML;
    }
}
