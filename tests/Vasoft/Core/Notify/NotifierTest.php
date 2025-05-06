<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify;

use PHPUnit\Framework\TestCase;
use Vasoft\Core\Notify\Contract\JobProcessorInterface;
use Vasoft\Core\Notify\Contract\SendServiceInterface;
use Vasoft\Core\Notify\Job\JobMapper;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Notify\Notifier
 */
final class NotifierTest extends TestCase
{
    public function testProcess(): void
    {
        $job1 = self::GetMockBuilder(JobProcessorInterface::class)->getMock();
        $job1->expects(self::once())->method('execute');
        $job1->expects(self::once())->method('getMessageStrings')->willReturn(['foo']);
        $job2 = self::GetMockBuilder(JobProcessorInterface::class)->getMock();
        $job2->expects(self::once())->method('execute');
        $job2->expects(self::once())->method('getMessageStrings')->willReturn(['bar']);
        /** @var SendServiceInterface $sender */
        $sender = self::GetMockBuilder(SendServiceInterface::class)->getMock();
        $sender->expects(self::once())->method('send')->with(['foo', '', 'bar']);

        $notifier = new Notifier([$job1, $job2], new JobMapper(), $sender);
        $notifier->process();
    }
}
