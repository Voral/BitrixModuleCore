<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Notify\Sender;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;

trait MockTrait
{
    use PHPMock;

    private bool $initialized = false;

    protected static int $mockFileGetContentsCount = 0;
    protected static array $mockFileGetContentsParam = [];
    protected static array $mockFileGetContentsContent = [];
    protected static array $mockFileGetContentsResult = [];
    protected static int $mockStreamContextCreateCount = 0;
    protected static array $mockStreamContextCreateParam = [];
    protected static array $mockStreamContextCreateResult = [];

    protected function initMocks(string $namespace = __NAMESPACE__): void
    {
        if (!$this->initialized) {
            $mockFileGetContents = $this->getFunctionMock($namespace, 'file_get_contents');
            $mockFileGetContents->expects(TestCase::any())->willReturnCallback(
                static function ($file, bool $useIncludePath, mixed $content): false|string {
                    ++self::$mockFileGetContentsCount;
                    self::$mockFileGetContentsParam[] = $file;
                    self::$mockFileGetContentsContent[] = $content;

                    return self::$mockFileGetContentsResult[$file] ?? false;
                },
            );
            $mockStreamContextCreate = $this->getFunctionMock($namespace, 'stream_context_create');
            $mockStreamContextCreate->expects(TestCase::any())->willReturnCallback(static function ($options) {
                $index = self::$mockStreamContextCreateCount;
                ++self::$mockStreamContextCreateCount;
                self::$mockStreamContextCreateParam[$index] = $options;

                return self::$mockStreamContextCreateResult[$index] ?? new \stdClass();
            });

            $this->initialized = true;
        }
    }

    protected function clearMockFileGetContents(array $result): void
    {
        self::$mockFileGetContentsCount = 0;
        self::$mockFileGetContentsParam = [];
        self::$mockFileGetContentsContent = [];
        self::$mockFileGetContentsResult = $result;
    }

    protected function clearMockStreamContextCreate(array $result): void
    {
        self::$mockStreamContextCreateCount = 0;
        self::$mockStreamContextCreateParam = [];
        self::$mockStreamContextCreateResult = $result;
    }
}
