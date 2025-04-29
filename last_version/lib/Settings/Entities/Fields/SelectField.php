<?php

declare(strict_types=1);
/** @noinspection PhpUnused */

namespace Vasoft\Core\Settings\Entities\Fields;

use Vasoft\Core\Settings\Field;

/**
 * Поле списка выбора. Тег select.
 */
class SelectField extends Field
{
    protected array $options = [];
    private bool $multiple = false;

    /**
     * Конфигурация опций списка выбора.
     *
     * @param array $options Ключ - значение, Величина - описание
     *
     * @return $this
     */
    public function configureOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Включение режим мультивыбора.
     *
     * @return $this
     */
    public function configureMultiple(bool $value = true): self
    {
        $this->multiple = $value;

        return $this;
    }

    /** @noinspection HtmlUnknownAttribute */
    public function renderInput(): string
    {
        $values = ($this->getter)();

        if (!is_array($values)) {
            $values = [$values];
        }
        $options = $this->options;

        $name = $this->code;
        if ($this->multiple) {
            $name .= '[]';
        }

        return sprintf(
            '<select name="%s" %s>%s</select>',
            $name,
            $this->multiple ? 'multiple' : '',
            array_reduce(
                array_keys($options),
                static fn($carry, $key) => $carry . sprintf(
                    '<option value="%s" %s>%s</option>',
                    $key,
                    in_array($key, $values, false) ? 'selected' : '',
                    $options[$key],
                ),
                '',
            ),
        );
    }
}
