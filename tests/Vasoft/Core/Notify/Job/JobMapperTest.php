<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Job;

use PHPUnit\Framework\TestCase;
use Vasoft\Core\Notify\Contract\JobProcessorInterface;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Notify\Job\JobMapper
 */
final class JobMapperTest extends TestCase
{
    public function testJobMapper(): void
    {
        $job1 = self::createMock(JobProcessorInterface::class);
        $job1->expects(self::once())->method('getMessageStrings')->willReturn([]);
        $job2 = self::createMock(JobProcessorInterface::class);
        $job2->expects(self::once())->method('getMessageStrings')->willReturn([
            'First Message from 2',
            'Second Message from 2',
        ]);
        $job3 = self::createMock(JobProcessorInterface::class);
        $job3->expects(self::once())->method('getMessageStrings')->willReturn([
            'First Message from 3',
            'Second Message from 3',
        ]);
        $mapper = new JobMapper();
        self::assertSame([
            'First Message from 2',
            'Second Message from 2',
            '',
            'First Message from 3',
            'Second Message from 3',
        ], $mapper->map([$job1, $job2, $job3]));
    }
}
