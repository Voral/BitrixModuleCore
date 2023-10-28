<?php

namespace Vasoft\Core\Settings\Entities\Fields;


use Vasoft\Core\Settings\Field;

/**
 * Поле текста. Тег textarea
 */
class TextAreaField extends Field
{
    protected int $height = 400;
    protected int $width = 800;

    public function renderInput(): string
    {
        return $this->renderInputAndValue(($this->getter)());
    }

    protected function renderInputAndValue(string $value): string
    {
        return <<<HTML
<textarea type="text" style="width:{$this->width}px;max-width:100%;height:{$this->height}px" name="$this->code">$value</textarea> 
HTML;
    }

    /**
     * Настройка высоты в пикселях
     * @param int $height
     * @return $this
     */
    public function configureHeight(int $height): static
    {
        $this->height = $height;
        return $this;
    }

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
}