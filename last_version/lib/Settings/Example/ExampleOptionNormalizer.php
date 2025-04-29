<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Example;

class ExampleOptionNormalizer
{
    public static function normalize(mixed $value): string
    {
        return ExampleOptions::valid($value) ? $value : '';
    }
}
