<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Changelog;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;

trait MockTrait
{
    use PHPMock;

    private bool $initialized = false;

    protected static int $mockFOpenCount = 0;
    protected static array $mockFOpenResult = [];
    protected static array $mockFOpenParam = [];
    protected static int $mockFCloseCount = 0;
    protected static array $mockFCloseParam = [];
    protected static int $mockFGetsCount = 0;
    protected static array $mockFGetsResult = [];

    protected static array $mockIsReadableResult = [];
    protected static int $mockIsReadableCount = 0;
    protected static array $mockIsReadableParam = [];

    protected function initMocks(string $namespace = __NAMESPACE__): void
    {
        if (!$this->initialized) {
            $mockFOpen = $this->getFunctionMock($namespace, 'fopen');
            $mockFOpen->expects(TestCase::any())->willReturnCallback(
                static function (string $path, string $mode): false|object {
                    self::$mockFOpenParam[] = [$path, $mode];
                    ++self::$mockFOpenCount;

                    return self::$mockFOpenResult[$path];
                },
            );
            $mockFGets = $this->getFunctionMock($namespace, 'fgets');
            $mockFGets->expects(TestCase::any())->willReturnCallback(
                static function ($stream, $length): false|string {
                    $index = self::$mockFGetsCount;
                    ++self::$mockFGetsCount;

                    return self::$mockFGetsResult[$index] ?? false;
                },
            );
            $mockFClose = $this->getFunctionMock($namespace, 'fclose');
            $mockFClose->expects(TestCase::any())->willReturnCallback(
                static function ($file): void {
                    self::$mockFCloseParam[] = $file;
                    ++self::$mockFCloseCount;
                },
            );

            $mockIsReadable = $this->getFunctionMock($namespace, 'is_readable');
            $mockIsReadable->expects(TestCase::any())->willReturnCallback(
                static function (string $file): bool {
                    self::$mockIsReadableParam[] = $file;
                    ++self::$mockIsReadableCount;

                    return self::$mockIsReadableResult[$file];
                },
            );
        }
    }

    protected function clearMockIsReadable(array $result): void
    {
        self::$mockIsReadableResult = $result;
        self::$mockIsReadableCount = 0;
        self::$mockIsReadableParam = [];
    }

    protected function clearMockFOpen(array $result): void
    {
        self::$mockFOpenCount = 0;
        self::$mockFOpenResult = $result;
        self::$mockFOpenParam = [];
    }

    protected function clearMockFClose(): void
    {
        self::$mockFCloseCount = 0;
        self::$mockFCloseParam = [];
    }

    protected function clearMockFGets(array $result): void
    {
        self::$mockFGetsCount = 0;
        self::$mockFGetsResult = $result;
    }
}
