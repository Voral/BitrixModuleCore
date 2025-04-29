<?php

declare(strict_types=1);

namespace Vendor\Example\Settings;
class ExampleOptionNormalizer
{
    public static function normalize(mixed $value): string
    {
        return ExampleOptions::valid($value) ? $value : '';
    }
}
