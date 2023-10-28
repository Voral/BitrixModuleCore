<?php

namespace Vasoft\Core\Settings\Example;

class ExampleOptionNormalizer
{
    public static function normalize(mixed $value): string
    {
        return ExampleOptions::valid($value) ? $value : '';
    }
}