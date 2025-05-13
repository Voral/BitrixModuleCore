<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Settings\Entities\Fields;

use PHPUnit\Framework\TestCase;
use Vasoft\Core\Settings\Entities\Fields\NotZeroIntField;

/**
 * @coversDefaultClass \Vasoft\Core\Settings\Entities\Fields\NotZeroIntField
 *
 * @internal
 */
final class NotZeroIntFieldTest extends TestCase
{
    /**
     * @dataProvider provideRenderInputCases
     */
    public function testRenderInput(
        string $name,
        string $label,
        int|string $value,
        string $expected,
        string $message,
    ): void {
        $filed = new NotZeroIntField($name, $label, static fn(): int|string => $value);
        self::assertSame($expected, $filed->renderInput(), $message);
    }

    public static function provideRenderInputCases(): iterable
    {
        yield ['name', 'label', 1, '<input type="text" maxlength="255" value="1" name="name" style="width:400px;max-width:100%;">', 'Value should be integer'];
        yield ['name1', 'label', '2', '<input type="text" maxlength="255" value="2" name="name1" style="width:400px;max-width:100%;">', 'String value should be integer'];
        yield ['name1', 'label', '2asd', '<input type="text" maxlength="255" value="2" name="name1" style="width:400px;max-width:100%;">', '"2asd" Value should be integer'];
        yield ['name', 'label3', '', '<input type="text" maxlength="255" value="" name="name" style="width:400px;max-width:100%;">', 'Value should be empty'];
        yield ['name', 'label', '0', '<input type="text" maxlength="255" value="" name="name" style="width:400px;max-width:100%;">', 'Value 0 should be empty'];
    }
}
