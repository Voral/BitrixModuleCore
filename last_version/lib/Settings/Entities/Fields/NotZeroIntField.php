<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Entities\Fields;

/**
 * Поле которое считается пустым если значение 0.
 */
class NotZeroIntField extends TextField
{
    public function renderInput(): string
    {
        $value = (int) htmlspecialchars(($this->getter)());
        if (0 === $value) {
            $value = '';
        }

        return $this->renderInputAndValue($value);
    }
}
