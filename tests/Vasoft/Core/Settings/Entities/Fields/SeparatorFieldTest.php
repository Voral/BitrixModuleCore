<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Entities\Fields;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Vasoft\Core\Settings\Entities\Fields\SeparatorField
 *
 * @internal
 */
final class SeparatorFieldTest extends TestCase
{
    public function testRenderInput(): void
    {
        $field = new SeparatorField();
        self::assertSame('', $field->renderInput(), 'SeparatorField::renderInput() returns empty string');
    }

    public function testRender(): void
    {
        $field = new SeparatorField();
        self::assertSame('<tr><td colspan="2"><hr></td></tr>', $field->render());
    }

    public function testAutoincrementCode(): void
    {
        $field1 = new SeparatorField();
        $field2 = new SeparatorField();
        self::assertStringStartsWith('SEP_', $field1->code);
        self::assertStringStartsWith('SEP_', $field2->code);
        self::assertNotSame($field1->code, $field2->code);
        self::assertSame('', $field2->title);
    }
}
