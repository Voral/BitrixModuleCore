<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Render;

use Bitrix\Main\Localization\Loc;
use Bitrix\Mocker\MockDefinition;
use PHPUnit\Framework\TestCase;
use Vasoft\Core\Settings\Entities\Fields\SeparatorField;
use Vasoft\Core\Settings\Entities\Fields\TextField;
use Vasoft\Core\Settings\Entities\Tab;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Settings\Render\Controller
 */
final class ControllerTest extends TestCase
{
    public function testWithTabWithRights(): void
    {
        $expects = <<<'HTML'
            ##BEGIN####BEGINNEXTTAB##            <tr>
                            <td style="width:50%;vertical-align:top">Example field</td>
                            <td style="width:50%;vertical-align:top"><input type="text" maxlength="255" value="test" name="EXAMPLE_STRING" style="width:400px;max-width:100%;"></td>
                        </tr><tr><td colspan="2"><hr></td></tr>##BEGINNEXTTAB##            <tr>
                            <td style="width:50%;vertical-align:top">Example field</td>
                            <td style="width:50%;vertical-align:top"><input type="text" maxlength="255" value="test" name="EXAMPLE_STRING" style="width:400px;max-width:100%;"></td>
                        </tr><tr><td colspan="2"><hr></td></tr>##BEGINNEXTTAB##
            HTML;

        Loc::cleanMockData('getMessage', defaultDefinition: new MockDefinition(result: 'test'));
        \CAdminTabControl::cleanMockData('Begin', defaultDefinition: new MockDefinition(output: '##BEGIN##'));
        \CAdminTabControl::cleanMockData(
            'BeginNextTab',
            defaultDefinition: new MockDefinition(
                output: '##BEGINNEXTTAB##',
            ),
        );

        $controller = new Controller([
            new Tab('div1', 'Tab 1', [
                new TextField('EXAMPLE_STRING', 'Example field', static fn() => 'test'),
                new SeparatorField(),
            ]),
            new Tab('div2', 'Tab new', [
                new TextField('EXAMPLE_STRING', 'Example field', static fn() => 'test'),
                new SeparatorField(),
            ]),
        ], true);
        ob_start();
        $tabControl = $controller->startTabControl('test');
        $controller->echoTabs($tabControl);
        $output = ob_get_clean();
        self::assertSame($expects, $output);
    }

    public function testWithTabWithOutRights(): void
    {
        $expects = <<<'HTML'
            ##BEGINNEXTTAB##            <tr>
                            <td style="width:50%;vertical-align:top">Example field</td>
                            <td style="width:50%;vertical-align:top"><input type="text" maxlength="255" value="test" name="EXAMPLE_STRING" style="width:400px;max-width:100%;"></td>
                        </tr><tr><td colspan="2"><hr></td></tr>##BEGINNEXTTAB##            <tr>
                            <td style="width:50%;vertical-align:top">Example field</td>
                            <td style="width:50%;vertical-align:top"><input type="text" maxlength="255" value="test" name="EXAMPLE_STRING" style="width:400px;max-width:100%;"></td>
                        </tr><tr><td colspan="2"><hr></td></tr>
            HTML;

        Loc::cleanMockData('getMessage', defaultDefinition: new MockDefinition(result: 'test'));
        \CAdminTabControl::cleanMockData('Begin', defaultDefinition: new MockDefinition(output: '##BEGIN##'));
        \CAdminTabControl::cleanMockData(
            'BeginNextTab',
            defaultDefinition: new MockDefinition(
                output: '##BEGINNEXTTAB##',
            ),
        );
        $controller = new Controller([
            new Tab('div1', 'Tab 1', [
                new TextField('EXAMPLE_STRING', 'Example field', static fn() => 'test'),
                new SeparatorField(),
            ]),
            new Tab('div2', 'Tab new', [
                new TextField('EXAMPLE_STRING', 'Example field', static fn() => 'test'),
                new SeparatorField(),
            ]),
        ], false);
        ob_start();
        $tabControl = $controller->startTabControl('test');
        $output = ob_get_clean();
        self::assertSame('##BEGIN##', $output);

        ob_start();
        $controller->echoTabs($tabControl);
        $output = ob_get_clean();
        self::assertSame($expects, $output);
    }

    public function testEmptyWithRights(): void
    {
        Loc::cleanMockData('getMessage', defaultDefinition: new MockDefinition(result: 'test'));
        \CAdminTabControl::cleanMockData('Begin', defaultDefinition: new MockDefinition(output: '##BEGIN##'));
        \CAdminTabControl::cleanMockData(
            'BeginNextTab',
            defaultDefinition: new MockDefinition(
                output: '##BEGINNEXTTAB##',
            ),
        );

        $controller = new Controller([], true);
        ob_start();
        $tabControl = $controller->startTabControl('test');
        $output = ob_get_clean();
        self::assertSame('##BEGIN##', $output);

        ob_start();
        $controller->echoTabs($tabControl);
        $output = ob_get_clean();
        self::assertSame('##BEGINNEXTTAB##', $output);
    }

    public function testEmpty(): void
    {
        \CAdminTabControl::cleanMockData('Begin', defaultDefinition: new MockDefinition(output: '##BEGIN##'));
        \CAdminTabControl::cleanMockData(
            'BeginNextTab',
            defaultDefinition: new MockDefinition(
                output: '##BEGINNEXTTAB##',
            ),
        );
        $controller = new Controller([], false);
        ob_start();
        $tabControl = $controller->startTabControl('test');
        $output = ob_get_clean();
        self::assertSame('##BEGIN##', $output);

        ob_start();
        $controller->echoTabs($tabControl);
        $output = ob_get_clean();
        self::assertSame('', $output);
    }
}
