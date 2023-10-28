<?php

namespace Vasoft\Core\Settings\Entities\Fields;

/**
 * Поле которое считается пустым если значение 0
 */
class NotZeroIntField extends TextField
{
    public function renderInput(): string
    {
        $value = (int)htmlspecialchars(($this->getter)());
        if ($value === 0) {
            $value = '';
        }
        return $this->renderInputAndValue($value);
    }
}