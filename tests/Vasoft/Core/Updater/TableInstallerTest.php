<?php

declare(strict_types=1);

namespace Vasoft\Core\Updater;

use Bitrix\Main\Application;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\Loader;
use Bitrix\Mocker\MockDefinition;
use PHPUnit\Framework\TestCase;
use Bitrix\Main\ORM\Entity;

/**
 * @internal
 *
 * @coversNothing
 */
final class TableInstallerTest extends TestCase
{
    public function testCheck(): void
    {
        $connection = self::createMock(\Bitrix\Main\DB\Connection::class);
        $connection->method('isTableExists')->willReturnCallback(static fn($tableName) => 'b_test' === $tableName);

        $table1 = self::getMockBuilder(Entity::class)
            ->onlyMethods(['getDBTableName'])
            ->getMock();
        $table1->method('getDBTableName')->willReturn('b_test');

        $table2 = self::getMockBuilder(Entity::class)
            ->onlyMethods(['getDBTableName'])
            ->getMock();
        $table2->method('getDBTableName')->willReturn('b_test2');

        $definition1 = new MockDefinition(['\Vendor\Example\Data\TestTable::class'], $table1);
        $definition2 = new MockDefinition(['\Vendor\Example\Data\TestTable2::class'], $table2);
        Entity::cleanMockData('getInstance', [$definition1, $definition2], namedMode: true);
        Entity::cleanMockData('createDbTable');
        Loader::cleanMockData('includeModule', defaultDefinition: new MockDefinition(result: true));
        Application::cleanMockData('getConnection', defaultDefinition: new MockDefinition(result: $connection));

        $installer = new TableInstaller('vendor.example', [
            '\Vendor\Example\Data\TestTable::class',
            '\Vendor\Example\Data\TestTable2::class',
        ]);
        $installer->check();
        self::assertSame(1, Loader::getMockedCounter('includeModule'));
        self::assertSame(1, Application::getMockedCounter('getConnection'));
        self::assertSame(2, Entity::getMockedCounter('getInstance'));
        self::assertSame(1, Entity::getMockedCounter('createDbTable'));
        //        self::assertSame(1, Entity::getMockedParams('getInstance', Entity::paramHash(['\Vendor\Example\Data\TestTable::class'])));
        // @todo Кто вызвал  createTable
    }
}
