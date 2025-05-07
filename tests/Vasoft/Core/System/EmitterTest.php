<?php

declare(strict_types=1);

namespace Vasoft\Core\System;

use Bitrix\Main\Localization\Loc;
use Vasoft\MockBuilder\Mocker\MockDefinition;
use PHPUnit\Framework\TestCase;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Vasoft\Core\Exceptions\DependencyExistsException;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\System\Emitter
 */
final class EmitterTest extends TestCase
{
    public function testEmitRemoveEmptyListeners(): void
    {
        Event::cleanMockData('__construct');
        Event::cleanMockData('send');
        Event::cleanMockData('getResults', [
            new MockDefinition(result: [new EventResult(EventResult::SUCCESS, [], 'vendor.module1')]),
        ]);
        EventResult::cleanMockData('getType', defaultDefinition: new MockDefinition(result: EventResult::SUCCESS));
        Emitter::emitRemove();
        self::assertSame(1, Event::getMockedCounter('__construct'));
        self::assertSame(1, Event::getMockedCounter('send'));
        self::assertSame(1, Event::getMockedCounter('getResults'));
    }

    public function testEmitRemove(): void
    {
        Loc::cleanMockData('getMessage', [
            new MockDefinition(['UNKNOWN_DEPENDENCY_MODULE'], 'Unknown module subscribed'),
            new MockDefinition(['ERROR_DEPENDENCY_EXISTS'], 'Dependency exists'),
        ], defaultDefinition: new MockDefinition(result: '???'), namedMode: true);
        Event::cleanMockData('__construct');
        Event::cleanMockData('send');
        Event::cleanMockData('getResults', [
            new MockDefinition(result: [
                new EventResult(EventResult::ERROR, '', 'vendor.module1'),
                new EventResult(EventResult::ERROR, 'Module2', ''),
                new EventResult(EventResult::ERROR, '', ''),
                new EventResult(EventResult::ERROR, 'Module4', 'vendor.module4'),
            ]),
        ]);
        EventResult::cleanMockData('getType', defaultDefinition: new MockDefinition(result: EventResult::ERROR));
        EventResult::cleanMockData('getParameters', [
            new MockDefinition(result: ''),
            new MockDefinition(result: 'Module2'),
            new MockDefinition(result: ''),
            new MockDefinition(result: 'Module4'),
        ]);
        EventResult::cleanMockData('getModuleId', [
            new MockDefinition(result: 'vendor.module1'),
            new MockDefinition(result: ''),
            new MockDefinition(result: ''),
            new MockDefinition(result: 'vendor.module4'),
        ]);

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
