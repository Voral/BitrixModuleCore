<?php

declare(strict_types=1);

namespace Vasoft\Core\System;

use Bitrix\Main\Localization\Loc;
use PHPUnit\Framework\TestCase;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Vasoft\Core\Exceptions\DependencyExistsException;

/**
 * @internal
 *
 * @сoversDefaultClass \Vasoft\Core\System\Emitter
 */
final class EmitterTest extends TestCase
{
    public function testEmitRemoveEmptyListeners(): void
    {
        Event::cleanMockData('__construct');
        Event::cleanMockData('send');
        Event::cleanMockData('getResults', defaultResult: [
            new EventResult(EventResult::SUCCESS, [], 'vendor.module1'),
        ]);
        EventResult::cleanMockData('getType', defaultResult: EventResult::SUCCESS);
        Emitter::emitRemove();
        self::assertSame(1, Event::getMockedCounter('__construct'));
        self::assertSame(1, Event::getMockedCounter('send'));
        self::assertSame(1, Event::getMockedCounter('getResults'));
    }

    public function testEmitRemove(): void
    {
        Loc::cleanMockData('getMessage', [
            Loc::paramHash(['UNKNOWN_DEPENDENCY_MODULE', null, null]) => 'Unknown module subscribed',
            Loc::paramHash(['ERROR_DEPENDENCY_EXISTS', null, null]) => 'Dependency exists',
        ], defaultResult: '???', namedMode: true);
        Event::cleanMockData('__construct');
        Event::cleanMockData('send');
        Event::cleanMockData('getResults', defaultResult: [
            new EventResult(EventResult::ERROR, '', 'vendor.module1'),
            new EventResult(EventResult::ERROR, 'Module2', ''),
            new EventResult(EventResult::ERROR, '', ''),
            new EventResult(EventResult::ERROR, 'Module4', 'vendor.module4'),
        ]);
        EventResult::cleanMockData('getType', defaultResult: EventResult::ERROR);
        EventResult::cleanMockData('getParameters', ['', 'Module2', '', 'Module4']);
        EventResult::cleanMockData('getModuleId', ['vendor.module1', '', '', 'vendor.module4']);

        try {
            Emitter::emitRemove();
            self::fail('Exception was not thrown');
        } catch (DependencyExistsException $e) {
            self::assertSame('Dependency exists', $e->getMessage());
            $expectedDependency = [
                'vendor.module1',
                'Module2',
                'Unknown module subscribed',
                'Module4 (vendor.module4)',
            ];

            self::assertSame($expectedDependency, $e->dependency);
        }
    }
}
