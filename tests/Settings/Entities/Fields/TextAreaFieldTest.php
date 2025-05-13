<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Settings\Entities\Fields;

use PHPUnit\Framework\TestCase;
use Vasoft\Core\Settings\Entities\Fields\TextAreaField;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Settings\Entities\Fields\TextAreaField
 */
final class TextAreaFieldTest extends TestCase
{
    public function testRenderInput(): void
    {
        $field = new TextAreaField('test', 'Test', static fn() => 'example');
        self::assertSame(
            '<textarea type="text" style="width:800px;max-width:100%;height:400px" name="test">example</textarea>',
            $field->renderInput(),
        );
    }

    public function testRenderInputCustomWidthHeight(): void
    {
        $field = new TextAreaField('test', 'Test', static fn() => '');
        $field
            ->configureWidth(100)
            ->configureHeight(50);
        self::assertSame(
            '<textarea type="text" style="width:100px;max-width:100%;height:50px" name="test"></textarea>',
            $field->renderInput(),
        );
    }
}
