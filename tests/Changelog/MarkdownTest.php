<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Changelog;

use Bitrix\Main\Localization\Loc;
use PHPUnit\Framework\TestCase;
use Vasoft\Core\Changelog\ChangelogEntry;
use Vasoft\Core\Changelog\ChangelogSection;
use Vasoft\Core\Changelog\Markdown;
use Vasoft\Core\Exceptions\FileException;
use Vasoft\MockBuilder\Mocker\MockDefinition;

include __DIR__ . '/MockTrait.php';

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Changelog\Markdown
 */
final class MarkdownTest extends TestCase
{
    use MockTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initMocks('\Vasoft\Core\Changelog');
    }

    public function testParseFromFileNotReadable(): void
    {
        $filePath = '/www/bitrix/modules/vendor_module/changelog.md';
        $this->clearMockIsReadable([$filePath => false]);
        Loc::cleanMockData('getMessage', defaultDefinition: new MockDefinition(result: 'Not writable'));
        $this->clearMockFOpen([]);

        $parser = new Markdown();
        self::expectException(FileException::class);
        self::expectExceptionMessage('Not writable');
        $parser->parseFromFile($filePath);
        self::assertSame([
            'VS_CORE_ERROR_FILE_NOT_READABLE',
            ['#FILE#' => $filePath],
        ], Loc::getMockedParams('getMessage', 0));
        self::assertSame(1, self::$mockIsReadableCount);
        self::assertSame(0, self::$mockFOpenCount);
    }

    public function testParseFromFileOpenError(): void
    {
        $filePath = '/www/bitrix/modules/vendor_module/changelog.md';
        $this->clearMockIsReadable([$filePath => true]);
        $this->clearMockFOpen([$filePath => false]);
        $this->clearMockFGets([]);

        Loc::cleanMockData('getMessage', defaultDefinition: new MockDefinition(result: 'Open error'));

        $parser = new Markdown();
        self::expectException(FileException::class);
        self::expectExceptionMessage('Open error');
        $parser->parseFromFile($filePath);
        self::assertSame(['VS_CORE_ERROR_FILE_OPEN', ['#FILE#' => $filePath]], Loc::getMockedParams('getMessage', 0));
        self::assertSame(1, self::$mockIsReadableCount);
        self::assertSame(1, self::$mockFOpenCount);
        self::assertSame(0, self::$mockFGetsCount);
    }

    public function testParseFromFile(): void
    {
        $filePath = '/www/bitrix/modules/vendor_module/changelog.md';
        $this->configDefault($filePath);
        $parser = new Markdown();

        $result = $parser->parseFromFile($filePath);
        self::assertCount(10, $result);
        self::assertInstanceOf(ChangelogEntry::class, $result[0]);
        self::assertSame('5.0.0', $result[0]->version);
        self::assertSame('2023-11-25', $result[0]->date->format('Y-m-d'));

        self::assertCount(1, $result[0]->changes);
        self::assertInstanceOf(ChangelogSection::class, $result[0]->changes[0]);
        self::assertSame('Added', $result[0]->changes[0]->title);
        self::assertCount(1, $result[0]->changes[0]->items);
        self::assertSame('Added new functionality 1', $result[0]->changes[0]->items[0]);

        $testResult = $result[1];
        self::assertCount(2, $testResult->changes);
        self::assertSame('Added', $testResult->changes[0]->title);
        self::assertSame(
            ['Added new functionality 2', 'Added some new functionality 2'],
            $testResult->changes[0]->items,
        );
        self::assertSame('Changed', $testResult->changes[1]->title);
        self::assertSame(['Changed something'], $testResult->changes[1]->items);

        $testResult = $result[2];
        self::assertSame('3.59.0', $testResult->version);
        self::assertCount(1, $testResult->changes);
        self::assertSame(['Added new functionality 3 with some multiline description'], $testResult->changes[0]->items);
    }

    public function testParseFromFileNoLimit(): void
    {
        $filePath = '/www/bitrix/modules/vendor_module/changelog.md';
        $this->configDefault($filePath);
        $parser = new Markdown();

        $result = $parser->parseFromFile($filePath, 0);
        self::assertCount(14, $result);

        $testResult = $result[13];
        self::assertSame('1.0.0', $testResult->version);
    }

    public function testParseFromFileLimit(): void
    {
        $filePath = '/www/bitrix/modules/vendor_module/changelog.md';
        $this->configDefault($filePath);
        $parser = new Markdown();

        $result = $parser->parseFromFile($filePath, 2);
        self::assertCount(2, $result);

        $testResult = $result[1];
        self::assertSame('4.0.0', $testResult->version);
        self::assertSame(12, self::$mockFGetsCount);
    }

    public function testParseFromFileFilterVersion(): void
    {
        $filePath = '/www/bitrix/modules/vendor_module/changelog.md';
        $this->configDefault($filePath);
        $parser = new Markdown();

        $result = $parser->parseFromFile($filePath, 0, filter: '2.2.0');
        self::assertCount(1, $result);
    }

    public function testParseFromFileFilterDate(): void
    {
        $filePath = '/www/bitrix/modules/vendor_module/changelog.md';
        $this->configDefault($filePath);
        $parser = new Markdown();

        $result = $parser->parseFromFile($filePath, filter: '2023-11-22');
        self::assertCount(1, $result);
    }

    public function testParseFromFileFilterWord(): void
    {
        $filePath = '/www/bitrix/modules/vendor_module/changelog.md';
        $this->configDefault($filePath);
        $parser = new Markdown();

        $result = $parser->parseFromFile($filePath, filter: 'targets phrase');
        self::assertCount(3, $result);
        self::assertSame('3.56.0', $result[0]->version);
        self::assertSame('1.1.0', $result[1]->version);
        self::assertSame('1.0.0', $result[2]->version);
    }

    private function configDefault(string $filePath): void
    {
        $this->clearMockIsReadable([$filePath => true]);
        $this->clearMockFOpen([$filePath => new \stdClass()]);
        $this->clearMockFGets([
            '#5.0.0 2023-11-25',
            '## Added',
            '* Added new functionality 1',
            '',
            '#4.0.0  2023-11-22',
            '## Added',
            '- Added new functionality 2',
            '- Added some new functionality 2',
            '## Changed',
            '* Changed something',
            '',
            '#v3.59.0  2022-11-25',
            '## Added',
            '* Added new functionality 3',
            'with some multiline description',
            '#3.58.0  2022-11-24',
            '## Added',
            '* Added new functionality 4',
            '#3.57.0  2022-11-23',
            '## Added',
            '* Added new functionality 5',
            '#3.56.0  2022-11-22',
            '## Added',
            '* Added new functionality 6',
            '* Added some new functionality 6 targets phrase',
            '#3.55.0  2022-11-21',
            '## Added',
            '* Added new functionality 7',
            '#3.54.0  2022-11-20',
            '## Added',
            '* Added new functionality 8',
            '',
            '#3.53.0  2022-11-19',
            '## Added',
            '* Added new functionality 9',
            ' target phrases',
            '#3.52.0  2022-11-18',
            '## Added',
            '* Added new functionality 10',
            '#2.2.0  2022-11-11',
            '## Added',
            '* Added new functionality 11',
            '#2.1.0  2022-11-10',
            '## Added',
            '* Added new functionality 12 phrases',
            '#1.1.0  2022-10-10',
            '## Added',
            '* Added new targets functionality 13 phrase',
            '# 1.0.0  2022-05-10',
            '## Added',
            '* Phrase targets',
        ]);
        $this->clearMockFClose();
    }
}
