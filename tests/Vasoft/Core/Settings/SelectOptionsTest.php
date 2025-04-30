<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Settings\SelectOptions
 */
final class SelectOptionsTest extends TestCase
{
    public function testGetList(): void
    {
        self::assertSame([
            'option_1' => 'Caption for option_1',
            'option_2' => 'Caption for option_2',
        ], FakeSelectField::getList());
    }

    public function testGetValid(): void
    {
        self::assertTrue(FakeSelectField::valid('option_1'), 'Valid value');
        self::assertFalse(FakeSelectField::valid('option_3'), 'Invalid value');
    }
}

enum FakeSelectField: string implements SelectOptionsInterface
{
    use SelectOptions;

    case OPTION_1 = 'option_1';
    case OPTION_2 = 'option_2';

    public function caption(): string
    {
        return 'Caption for ' . $this->value;
    }
}
