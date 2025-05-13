<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Notify\Job;

use PHPUnit\Framework\TestCase;
use Vasoft\Core\Notify\Job\SystemInfo;

include __DIR__ . '/MockTrait.php';

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Notify\Job\SystemInfo
 */
final class SystemInfoTest extends TestCase
{
    use MockTrait;

    /**
     * @dataProvider provideGetMessageStringsCases
     */
    public function testGetMessageStrings(int $bytes, string $expected): void
    {
        self::initMocks('Vasoft\Core\Notify\Job');
        $this->clearMockDiskFreeSpace([
            '/' => $bytes,
        ]);
        $job = new SystemInfo();
        $job->execute();
        self::assertSame('Free space: ' . $expected, $job->getMessageStrings()[0]);
        self::assertCount(1, $job->getMessageStrings());
    }

    public static function provideGetMessageStringsCases(): iterable
    {
        return [
            [12345678432, '11.50'],
            [987654321, '0.92'],
            [10242445, '0.01'],
        ];
    }
}
