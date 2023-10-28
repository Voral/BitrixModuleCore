<?php

namespace Vasoft\Core\Settings\Example;

use Vasoft\Core\Settings\SelectOptions;
use Vasoft\Core\Settings\SelectOptionsInterface;

enum ExampleOptions: string
    implements SelectOptionsInterface
{
    use SelectOptions;

    case VALUE_1 = 'yandex';
    case VALUE_2 = 'google';
    case VALUE_3 = 'rambler';

    public function caption(): string
    {
        return match ($this) {
            self::VALUE_1 => 'Yandex',
            self::VALUE_2 => 'Google',
            self::VALUE_3 => 'Rambler',
        };
    }
}