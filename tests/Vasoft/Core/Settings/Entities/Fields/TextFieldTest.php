<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Entities\Fields;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Settings\Entities\Fields\TextField
 */
final class TextFieldTest extends TestCase
{
    public function testRenderInputStandard(): void
    {
        $field = new TextField('test', 'Example field', static fn() => 'test');
        self::assertSame(
            '<input type="text" maxlength="255" value="test" name="test" style="width:400px;max-width:100%;">',
            $field->renderInput(),
        );
    }

    public function testRenderInputCustomWidth(): void
    {
        $field = new TextField('test', 'Example field', static fn() => '');
        $field->configureWidth(100);
        self::assertSame(
            '<input type="text" maxlength="255" value="" name="test" style="width:100px;max-width:100%;">',
            $field->renderInput(),
        );
    }

    public function testRenderInputMastConvertHTML(): void
    {
        $field = new TextField('test', 'Example field', static fn() => '<b>Example</b>');
        $field->configureWidth(10);
        self::assertSame(
            '<input type="text" maxlength="255" value="&lt;b&gt;Example&lt;/b&gt;" name="test" style="width:10px;max-width:100%;">',
            $field->renderInput(),
        );
    }
}
