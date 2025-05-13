<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Settings\Entities;

use PHPUnit\Framework\TestCase;
use Vasoft\Core\Settings\Entities\Tab;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Settings\Entities\Tab
 */
final class TabTest extends TestCase
{
    public function testWithDefaults(): void
    {
        $tab = new Tab('name', 'Tab Name', []);
        self::assertSame('name', $tab->divId);
        self::assertSame('Tab Name', $tab->name);
        self::assertSame('Tab Name', $tab->title);
        self::assertSame('', $tab->onSelectJs);
    }

    public function testMapWithoutJs(): void
    {
        $tab = new Tab('name', 'Tab Name', []);
        self::assertSame([
            'DIV' => 'name',
            'TAB' => 'Tab Name',
            'TITLE' => 'Tab Name',
        ], $tab->map());
    }

    public function testMapWiths(): void
    {
        $tab = new Tab('name', 'Tab Name', [], onSelectJs: 'alert(1)');
        self::assertSame([
            'DIV' => 'name',
            'TAB' => 'Tab Name',
            'TITLE' => 'Tab Name',
            'ONSELECT' => 'alert(1)',
        ], $tab->map());
    }
}
