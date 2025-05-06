<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Job;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;

trait MockTrait
{
    use PHPMock;

    private bool $initialized = false;

    protected static int $mockDiskFreeSpaceCount = 0;
    protected static array $mockDiskFreeSpaceResult = [];
    protected static array $mockDiskFreeSpaceParam = [];

    protected function initMocks(): void
    {
        if (!$this->initialized) {
            $mockDiskFreeSpace = $this->getFunctionMock(__NAMESPACE__, 'disk_free_space');
            $mockDiskFreeSpace->expects(TestCase::any())->willReturnCallback(
                static function (string $path): int {
                    self::$mockDiskFreeSpaceParam[] = $path;
                    ++self::$mockDiskFreeSpaceCount;

                    return self::$mockDiskFreeSpaceResult[$path];
                },
            );
            $this->initialized = true;
        }
    }

    protected function clearMockDiskFreeSpace(array $result): void
    {
        self::$mockDiskFreeSpaceCount = 0;
        self::$mockDiskFreeSpaceResult = $result;
        self::$mockDiskFreeSpaceParam = [];
    }
}
