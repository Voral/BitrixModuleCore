<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Settings\Entities\Fields;

use Bitrix\Main\Localization\Loc;
use PHPUnit\Framework\TestCase;
use Vasoft\Core\Settings\Entities\Fields\BooleanField;
use Vasoft\MockBuilder\Mocker\MockDefinition;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Settings\Entities\Fields\BooleanField
 */
final class BooleanFieldTest extends TestCase
{
    /**
     * @dataProvider provideRenderInputCases
     */
    public function testRenderInput(
        string $name,
        string $title,
        string $value,
        string $labelYes,
        string $labelNo,
        string $expected,
        string $message,
    ): void {
        Loc::cleanMockData('getMessage', [
            new MockDefinition(['BOOL_FIELD_YES'], $labelYes),
            new MockDefinition(['BOOL_FIELD_NO'], $labelNo),
        ], namedMode: true);

        $output = (new BooleanField($name, $title, static fn() => $value))->renderInput();
        self::assertSame($expected, $output, $message);
    }

    public static function provideRenderInputCases(): iterable
    {
        return [
            [
                'example',
                'Test Field',
                'Y',
                'Yes',
                'No',
                <<<'HTML'
                    <input type="radio" value="Y" name="example" checked> Yes&nbsp;&nbsp;
                    <input type="radio" value="N" name="example"> No
                    HTML,
                'Value true',
            ],
            [
                'example',
                'Test Field',
                'y',
                'Yes',
                'No',
                <<<'HTML'
                    <input type="radio" value="Y" name="example" checked> Yes&nbsp;&nbsp;
                    <input type="radio" value="N" name="example"> No
                    HTML,
                'Value true small letter',
            ],
            [
                'example1',
                'Test',
                'N',
                'YES',
                'NO',
                <<<'HTML'
                    <input type="radio" value="Y" name="example1"> YES&nbsp;&nbsp;
                    <input type="radio" value="N" name="example1" checked> NO
                    HTML,
                'Value false',
            ],
            [
                'example1',
                'Test',
                'z',
                'YES',
                'NO',
                <<<'HTML'
                    <input type="radio" value="Y" name="example1"> YES&nbsp;&nbsp;
                    <input type="radio" value="N" name="example1" checked> NO
                    HTML,
                'Value unknown',
            ],
        ];
    }
}
