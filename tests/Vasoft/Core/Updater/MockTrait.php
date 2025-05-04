<?php

declare(strict_types=1);

namespace Vasoft\Core\Updater;

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
    protected static int $mockFWriteCount = 0;
    protected static array $mockFWriteParam = [];
    protected static array $mockFWriteContent = [];
    protected static array $mockFileExistsParam = [];
    protected static array $mockFileExistsResult = [];
    protected static int $mockFileExistsCount = 0;
    protected static int $mockUnlinkCount = 0;
    protected static array $mockUnlinkParam = [];
    protected static int $mockFilePutContentsCount = 0;
    protected static array $mockFilePutContentsParam = [];
    protected static array $mockFilePutContentsContent = [];

    protected static array $mockRealPathParam = [];
    protected static int $mockRealPathCount = 0;
    protected static array $mockRealPathResult = [];

    protected function initMocks(): void
    {
        if (!$this->initialized) {
            $mockFOpen = $this->getFunctionMock(__NAMESPACE__, 'fopen');
            $mockFOpen->expects(TestCase::any())->willReturnCallback(
                static function (string $path, string $mode): false|object {
                    self::$mockFOpenParam[] = [$path, $mode];
                    ++self::$mockFOpenCount;

                    return self::$mockFOpenResult[$path];
                },
            );
            $mockFWrite = $this->getFunctionMock(__NAMESPACE__, 'fwrite');
            $mockFWrite->expects(TestCase::any())->willReturnCallback(
                static function ($file, string $content): void {
                    self::$mockFWriteParam[] = [$file, $content];
                    $objectId = spl_object_id($file);
                    self::$mockFWriteContent[$objectId] = (self::$mockFWriteContent[$objectId] ?? '') . $content;
                    ++self::$mockFWriteCount;
                },
            );
            $mockFClose = $this->getFunctionMock(__NAMESPACE__, 'fclose');
            $mockFClose->expects(TestCase::any())->willReturnCallback(
                static function ($file): void {
                    self::$mockFCloseParam[] = $file;
                    ++self::$mockFCloseCount;
                },
            );
            $mockFileExists = $this->getFunctionMock(__NAMESPACE__, 'file_exists');
            $mockFileExists->expects(TestCase::any())->willReturnCallback(static function ($file): bool {
                self::$mockFileExistsParam[] = $file;
                ++self::$mockFileExistsCount;

                return self::$mockFileExistsResult[$file];
            });
            $mockFilePutContents = $this->getFunctionMock(__NAMESPACE__, 'file_put_contents');
            $mockFilePutContents->expects(TestCase::any())->willReturnCallback(
                static function ($file, string $content): int {
                    ++self::$mockFilePutContentsCount;
                    self::$mockFilePutContentsParam[] = $file;
                    self::$mockFilePutContentsContent[] = $content;

                    return strlen($content);
                },
            );
            $mockUnlink = $this->getFunctionMock(__NAMESPACE__, 'unlink');
            $mockUnlink->expects(TestCase::any())->willReturnCallback(static function ($file): bool {
                ++self::$mockUnlinkCount;
                self::$mockUnlinkParam[] = $file;

                return true;
            });

            $mockRealPath = $this->getFunctionMock(__NAMESPACE__, 'realpath');
            $mockRealPath->expects(TestCase::any())->willReturnCallback(static function ($path): string {
                self::$mockRealPathParam[] = $path;
                ++self::$mockRealPathCount;

                return self::$mockRealPathResult[$path];
            });
            $this->initialized = true;
        }
    }

    protected function clearMockFileExists(array $result): void
    {
        self::$mockFileExistsCount = 0;
        self::$mockFileExistsResult = $result;
        self::$mockFileExistsParam = [];
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

    protected function clearMockFWrite(): void
    {
        self::$mockFWriteCount = 0;
        self::$mockFWriteParam = [];
        self::$mockFWriteContent = [];
    }

    protected function clearMockFilePutContents(): void
    {
        self::$mockFilePutContentsCount = 0;
        self::$mockFilePutContentsParam = [];
        self::$mockFilePutContentsContent = [];
    }

    protected function clearMockUnlink(): void
    {
        self::$mockUnlinkCount = 0;
        self::$mockUnlinkParam = [];
    }

    protected function clearMockRealPath(array $result): void
    {
        self::$mockRealPathParam = [];
        self::$mockRealPathCount = 0;
        self::$mockRealPathResult = $result;
    }
}
