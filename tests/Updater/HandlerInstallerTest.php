<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Updater;

use Bitrix\Main\Application;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\DB\Result;
use Bitrix\Main\EventManager;
use PHPUnit\Framework\TestCase;
use Vasoft\Core\Updater\HandlerDto;
use Vasoft\Core\Updater\HandlerInstaller;
use Vasoft\MockBuilder\Mocker\MockDefinition;

/**
 * @internal
 *
 * @coversDefaultClass \Bitrix\Vasoft\Core\Updater\HandlerInstaller
 */
final class HandlerInstallerTest extends TestCase
{
    public function testClean(): void
    {
        $moduleId = 'vendor.test';
        $query = "SELECT FROM_MODULE_ID, MESSAGE_ID, TO_CLASS, TO_METHOD  FROM b_module_to_module WHERE TO_MODULE_ID = 'vendor.test'";

        $manager = self::createMock(EventManager::class);
        $manager->expects(self::exactly(2))
            ->method('unRegisterEventHandler')
            ->willReturnCallback(static function (...$args) use (&$callCount, $moduleId): void {
                ++$callCount;

                if (1 === $callCount) {
                    self::assertSame('main', $args[0]);
                    self::assertSame('onAfterUserAdd', $args[1]);
                    self::assertSame($moduleId, $args[2]);
                    self::assertSame('ExampleClass', $args[3]);
                    self::assertSame('handler1', $args[4]);
                } elseif (2 === $callCount) {
                    self::assertSame('main', $args[0]);
                    self::assertSame('onAfterUserUpdate', $args[1]);
                    self::assertSame($moduleId, $args[2]);
                    self::assertSame('ExampleClass', $args[3]);
                    self::assertSame('handler2', $args[4]);
                }
            });

        EventManager::cleanMockData('getInstance', defaultDefinition: new MockDefinition(result: $manager));

        $connection = self::createMock(Connection::class);
        $result = self::createMock(Result::class);
        $result->expects(self::once())->method('fetchAll')->willReturn([
            [
                'FROM_MODULE_ID' => 'main',
                'MESSAGE_ID' => 'onAfterUserAdd',
                'TO_CLASS' => 'ExampleClass',
                'TO_METHOD' => 'handler1',
            ],
            [
                'FROM_MODULE_ID' => 'main',
                'MESSAGE_ID' => 'onAfterUserUpdate',
                'TO_CLASS' => 'ExampleClass',
                'TO_METHOD' => 'handler2',
            ],
        ]);
        $connection->expects(self::once())
            ->method('query')
            ->with(self::equalTo($query))
            ->willReturn($result);
        Application::cleanMockData('getConnection', defaultDefinition: new MockDefinition(result: $connection));

        $installer = new HandlerInstaller($moduleId, []);
        $installer->clean();
    }

    public function testCheckEmptyInput(): void
    {
        $moduleId = 'vendor.test';
        $query = "SELECT FROM_MODULE_ID, MESSAGE_ID, TO_CLASS, TO_METHOD  FROM b_module_to_module WHERE TO_MODULE_ID = 'vendor.test'";

        $manager = self::createMock(EventManager::class);
        $manager->expects(self::exactly(2))
            ->method('unRegisterEventHandler')
            ->willReturnCallback(static function (...$args) use (&$callCount, $moduleId): void {
                ++$callCount;

                if (1 === $callCount) {
                    self::assertSame('main', $args[0]);
                    self::assertSame('onAfterUserAdd', $args[1]);
                    self::assertSame($moduleId, $args[2]);
                    self::assertSame('ExampleClass', $args[3]);
                    self::assertSame('handler1', $args[4]);
                } elseif (2 === $callCount) {
                    self::assertSame('main', $args[0]);
                    self::assertSame('onAfterUserUpdate', $args[1]);
                    self::assertSame($moduleId, $args[2]);
                    self::assertSame('ExampleClass', $args[3]);
                    self::assertSame('handler2', $args[4]);
                }
            });

        EventManager::cleanMockData('getInstance', defaultDefinition: new MockDefinition(result: $manager));

        $connection = self::createMock(Connection::class);
        $result = self::createMock(Result::class);
        $result->expects(self::once())->method('fetchAll')->willReturn([
            [
                'FROM_MODULE_ID' => 'main',
                'MESSAGE_ID' => 'onAfterUserAdd',
                'TO_CLASS' => 'ExampleClass',
                'TO_METHOD' => 'handler1',
            ],
            [
                'FROM_MODULE_ID' => 'main',
                'MESSAGE_ID' => 'onAfterUserUpdate',
                'TO_CLASS' => 'ExampleClass',
                'TO_METHOD' => 'handler2',
            ],
        ]);
        $connection->expects(self::once())
            ->method('query')
            ->with(self::equalTo($query))
            ->willReturn($result);
        Application::cleanMockData('getConnection', defaultDefinition: new MockDefinition(result: $connection));

        $installer = new HandlerInstaller($moduleId, []);
        $installer->check();
    }

    public function testCheck(): void
    {
        $moduleId = 'vendor.test';
        $query = "SELECT FROM_MODULE_ID, MESSAGE_ID, TO_CLASS, TO_METHOD  FROM b_module_to_module WHERE TO_MODULE_ID = 'vendor.test'";

        $manager = self::createMock(EventManager::class);
        $manager->expects(self::once())
            ->method('unRegisterEventHandler')
            ->with(
                self::equalTo('main'),
                self::equalTo('onAfterUserUpdate'),
                self::equalTo($moduleId),
                self::equalTo('ExampleClass'),
                self::equalTo('handler2'),
            );
        $manager->expects(self::once())
            ->method('registerEventHandler')
            ->with(
                self::equalTo('sale'),
                self::equalTo('onAfterCardAdd'),
                self::equalTo($moduleId),
                self::equalTo('AnotherClass'),
                self::equalTo('handlerSome'),
            );

        EventManager::cleanMockData('getInstance', defaultDefinition: new MockDefinition(result: $manager));

        $connection = self::createMock(Connection::class);
        $result = self::createMock(Result::class);
        $result->expects(self::once())->method('fetchAll')->willReturn([
            [
                'FROM_MODULE_ID' => 'main',
                'MESSAGE_ID' => 'onAfterUserAdd',
                'TO_CLASS' => 'ExampleClass',
                'TO_METHOD' => 'handler1',
            ],
            [
                'FROM_MODULE_ID' => 'main',
                'MESSAGE_ID' => 'onAfterUserUpdate',
                'TO_CLASS' => 'ExampleClass',
                'TO_METHOD' => 'handler2',
            ],
        ]);
        $connection->expects(self::once())
            ->method('query')
            ->with(self::equalTo($query))
            ->willReturn($result);
        Application::cleanMockData('getConnection', defaultDefinition: new MockDefinition(result: $connection));

        $installer = new HandlerInstaller($moduleId, [
            new HandlerDto('main', 'onAfterUserAdd', 'ExampleClass', 'handler1'),
            new HandlerDto('sale', 'onAfterCardAdd', 'AnotherClass', 'handlerSome'),
        ]);
        $installer->check();
    }
}
