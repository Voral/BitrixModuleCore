<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Updater;

use Bitrix\Main\Application;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Entity;
use PHPUnit\Framework\TestCase;
use Vasoft\Core\Updater\TableInstaller;
use Vasoft\MockBuilder\Mocker\MockDefinition;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Updater\TableInstaller
 */
final class TableInstallerTest extends TestCase
{
    public function testCheck(): void
    {
        $connection = self::createMock(Connection::class);
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
        self::assertSame(
            ['\Vendor\Example\Data\TestTable2::class'],
            Entity::getMockedParams('getInstance', $definition2->getIndex()),
        );
    }

    public function testClean(): void
    {
        $connection = self::createMock(Connection::class);
        $connection->method('isTableExists')->willReturnCallback(static fn($tableName) => 'b_test' === $tableName);
        $connection
            ->expects(self::once())
            ->method('queryExecute')
            ->with(self::equalTo('drop table if exists b_test'));

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
        Loader::cleanMockData('includeModule', defaultDefinition: new MockDefinition(result: true));
        Application::cleanMockData('getConnection', defaultDefinition: new MockDefinition(result: $connection));

        $installer = new TableInstaller('vendor.example', [
            '\Vendor\Example\Data\TestTable::class',
            '\Vendor\Example\Data\TestTable2::class',
        ]);
        $installer->clean();
        self::assertSame(1, Loader::getMockedCounter('includeModule'));
        self::assertSame(1, Application::getMockedCounter('getConnection'));
        self::assertSame(2, Entity::getMockedCounter('getInstance'));
    }
}
