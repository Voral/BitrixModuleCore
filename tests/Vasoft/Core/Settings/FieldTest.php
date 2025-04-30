<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings;

use PHPUnit\Framework\TestCase;
use Vasoft\Core\Settings\Entities\Fields\TextField;

/**
 * @coversDefaultClass \Vasoft\Core\Settings\Field
 *
 * @internal
 */
final class FieldTest extends TestCase
{
    public function testRender(): void
    {
        $expected = <<<'HTML'
                        <tr>
                            <td style="width:50%;vertical-align:top">Example field</td>
                            <td style="width:50%;vertical-align:top"><input type="text" maxlength="255" value="test" name="test" style="width:400px;max-width:100%;"></td>
                        </tr>
            HTML;
        $field = new TextField('test', 'Example field', static fn() => 'test');
        self::assertSame($expected, $field->render());
    }

    public function testRenderWithNote(): void
    {
        $expected = <<<'HTML'
                        <tr>
                            <td style="width:50%;vertical-align:top">Some value</td>
                            <td style="width:50%;vertical-align:top"><input type="text" maxlength="255" value="" name="val" style="width:400px;max-width:100%;"></td>
                        </tr><tr><td colspan="2" style="padding: 8px 10%">Example note</td></tr>
            HTML;
        $field = new TextField('val', 'Some value', static fn() => '');
        $field->configureNote('Example note');
        self::assertSame($expected, $field->render());
        self::assertSame('val', $field->getCode(), 'The code should be the same.');
    }
}
