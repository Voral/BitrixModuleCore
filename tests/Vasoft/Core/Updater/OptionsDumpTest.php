<?php

declare(strict_types=1);

namespace Vasoft\Core\Updater;

use Vasoft\MockBuilder\Mocker\MockDefinition;
use PHPUnit\Framework\TestCase;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ArgumentNullException;
use Vasoft\Core\Settings\ModuleSettings;

include_once __DIR__ . '/MockTrait.php';

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Updater\OptionsDump
 */
final class OptionsDumpTest extends TestCase
{
    use MockTrait;

    public static int $fileIndex = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initMocks();
    }

    /**
     * @throws ArgumentNullException
     * @throws \ReflectionException
     *
     * @dataProvider provideDumpCases
     */
    public function testDump(
        string $moduleId,
        string $fileName,
        array $options,
        array $filter,
        false|string $siteId,
        array $expectedOptions,
    ): void {
        Option::cleanMockData('getForModule', [new MockDefinition(result: $options)]);
        $file = new \stdClass();
        $this->clearMockFOpen([$fileName . '.php' => $file]);
        $this->clearMockFWrite();
        $this->clearMockFClose();
        $dumper = new OptionsDump('/');
        $dumper->dump($moduleId, $fileName, $filter, $siteId);

        $expected = '<?php' . PHP_EOL . '$options = ' . var_export($expectedOptions, true) . ';' . PHP_EOL;

        self::assertSame($expected, self::$mockFWriteContent[spl_object_id($file)]);
        self::assertSame([$moduleId, $siteId], Option::getMockedParams('getForModule', 0));
        self::assertSame([$fileName . '.php', 'w'], self::$mockFOpenParam[0]);
        self::assertSame(1, self::$mockFOpenCount);
        self::assertSame(1, self::$mockFCloseCount);
    }

    public static function provideDumpCases(): iterable
    {
        return [
            ['vendor1.module1', 'options1', [], [], '', []],
            ['vendor1.module2', 'options2', [], [], 's1', []],
            [
                'vendor1.module1',
                'options3.',
                ['option1' => 'value1', 'option2' => 'value2'],
                [],
                '',
                ['option1' => 'value1', 'option2' => 'value2'],
            ],
            [
                'vendor1.module1',
                'options4',
                ['option1' => 'value1', 'option2' => 'value2'],
                ['option2'],
                '',
                ['option1' => 'value1'],
            ],
        ];
    }

    public function testDumpSettings(): void
    {
        $options = ['option1' => 'value1', 'option2' => 'value2'];
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: $options));
        $file = new \stdClass();
        $this->clearMockFOpen(['/vendor.module_' . date('YmdHis') . '.php' => $file]);
        $this->clearMockFWrite();
        $this->clearMockFClose();
        $dumper = new OptionsDump('/');
        $settings = TestModuleSettings::getInstance();
        $dumper->dumpSettings($settings);

        $expected = '<?php' . PHP_EOL . '$options = ' . var_export($options, true) . ';' . PHP_EOL;
        self::assertSame($expected, self::$mockFWriteContent[spl_object_id($file)]);
        self::assertSame([$settings->moduleCode, $settings->siteId], Option::getMockedParams('getForModule', 0));
        self::assertSame(1, self::$mockFOpenCount);
        self::assertSame(1, self::$mockFCloseCount);
    }

    public function testDumpPathFromServer(): void
    {
        $bkp = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $_SERVER['DOCUMENT_ROOT'] = '/usr/local/apache2/htdocs';

        $options = ['option1' => 'value1', 'option2' => 'value2'];
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: $options));
        $file = new \stdClass();
        $this->clearMockFOpen(['/usr/local/apache2/htdocs/vendor.module_' . date('YmdHis') . '.php' => $file]);
        $this->clearMockFWrite();
        $this->clearMockFClose();
        $dumper = new OptionsDump();
        $settings = TestModuleSettings::getInstance();
        $dumper->dumpSettings($settings);

        $expected = '<?php' . PHP_EOL . '$options = ' . var_export($options, true) . ';' . PHP_EOL;
        self::assertSame($expected, self::$mockFWriteContent[spl_object_id($file)]);
        self::assertSame([$settings->moduleCode, $settings->siteId], Option::getMockedParams('getForModule', 0));
        self::assertSame(1, self::$mockFOpenCount);
        self::assertSame(1, self::$mockFCloseCount);
        $_SERVER['DOCUMENT_ROOT'] = $bkp;
    }

    public function testDumpSettingsFiltered(): void
    {
        $options = ['option1' => 'value1', 'option2' => 'value2'];
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: $options));
        $file = new \stdClass();
        $this->clearMockFOpen(['/vendor.module_' . date('YmdHis') . '.php' => $file]);
        $this->clearMockFWrite();
        $this->clearMockFClose();
        $dumper = new OptionsDump('/');
        $settings = TestModuleSettings::getInstance();
        $dumper->dumpSettings($settings, ['option1']);

        $expected = '<?php' . PHP_EOL . '$options = ' . var_export(['option2' => 'value2'], true) . ';' . PHP_EOL;
        self::assertSame($expected, self::$mockFWriteContent[spl_object_id($file)]);
        self::assertSame([$settings->moduleCode, $settings->siteId], Option::getMockedParams('getForModule', 0));
        self::assertSame(1, self::$mockFOpenCount);
        self::assertSame(1, self::$mockFCloseCount);
    }

    public function testDumpSettingsFilteredFileName(): void
    {
        $options = ['option1' => 'value1', 'option2' => 'value2'];
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: $options));
        $file = new \stdClass();
        $this->clearMockFOpen(['/test_' . date('YmdHis') . '-vendor.module.php' => $file]);
        $this->clearMockFWrite();
        $this->clearMockFClose();
        $dumper = new OptionsDump('/', 'test_#TIME#-#MODULE_ID#');
        $settings = TestModuleSettings::getInstance();
        $dumper->dumpSettings($settings, ['option1']);

        $expected = '<?php' . PHP_EOL . '$options = ' . var_export(['option2' => 'value2'], true) . ';' . PHP_EOL;
        self::assertSame($expected, self::$mockFWriteContent[spl_object_id($file)]);
        self::assertSame([$settings->moduleCode, $settings->siteId], Option::getMockedParams('getForModule', 0));
        self::assertSame(1, self::$mockFOpenCount);
        self::assertSame(1, self::$mockFCloseCount);
    }

    public function testRestoreSettingsNoFilterNoBackUp(): void
    {
        $options = ['option1' => 'value1', 'option2' => 'value2'];
        $fileName = __DIR__ . '/fake/options_fake.php';
        $this->clearMockFileExists([$fileName => true]);
        $settings = self::createMock(ModuleSettings::class);
        $settings->expects(self::once())->method('saveFromArray')->with($options);
        $dumper = new OptionsDump('/');
        $dumper->restoreSettings($settings, $fileName, [], false);
        self::assertSame(1, self::$mockFileExistsCount);
        self::assertSame($fileName, self::$mockFileExistsParam[0]);
    }

    public function testRestoreSettingsFilteredNoBackUp(): void
    {
        $fileName = __DIR__ . '/fake/options_fake.php';
        $this->clearMockFileExists([$fileName => true]);
        $settings = self::createMock(ModuleSettings::class);
        $settings->expects(self::once())->method('saveFromArray')->with(['option1' => 'value1']);
        $dumper = new OptionsDump('/');
        $dumper->restoreSettings($settings, $fileName, ['option2'], false);
        self::assertSame(1, self::$mockFileExistsCount);
        self::assertSame($fileName, self::$mockFileExistsParam[0]);
    }

    public function testRestoreSettingsFilteredBackUp(): void
    {
        $fileName = __DIR__ . '/fake/options_fake.php';
        $this->clearMockFileExists([$fileName => true]);
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: ['option1' => 'value1']));
        $file = new \stdClass();
        $bkpFileName = '/vendor.module-' . date('YmdHis') . '-bkp.php';
        $this->clearMockFOpen([$bkpFileName => $file]);
        $this->clearMockFWrite();
        $this->clearMockFClose();
        $settings = TestModuleSettings::getInstance();

        $dumper = new OptionsDump('/');
        $dumper->restoreSettings($settings, $fileName, ['option2']);
        self::assertSame(1, $settings->saveFromArrayCallCount);
        self::assertSame(['option1' => 'value1'], $settings->saveFromArrayParams[0]);
        self::assertSame(1, self::$mockFileExistsCount);
        self::assertSame($fileName, self::$mockFileExistsParam[0]);
        self::assertSame(1, self::$mockFOpenCount);
        self::assertSame(1, self::$mockFCloseCount);
    }

    public function testRestoreNotFilteredNoBackUp(): void
    {
        $fileName = __DIR__ . '/fake/options_fake.php';
        $this->clearMockFileExists([$fileName => true]);
        Option::cleanMockData('set');
        $dumper = new OptionsDump('/');
        $dumper->restore('vendor.module4', $fileName, [], false, false);
        self::assertSame(2, Option::getMockedCounter('set'));
        self::assertSame(['vendor.module4', 'option1', 'value1', false], Option::getMockedParams('set', 0));
        self::assertSame(['vendor.module4', 'option2', 'value2', false], Option::getMockedParams('set', 1));
    }

    public function testRestoreFilteredNoBackUp(): void
    {
        $fileName = __DIR__ . '/fake/options_fake.php';
        $this->clearMockFileExists([$fileName => true]);
        Option::cleanMockData('set');
        $dumper = new OptionsDump('/');
        $dumper->restore('vendor.module4', $fileName, ['option1'], false, false);
        self::assertSame(1, Option::getMockedCounter('set'));
        self::assertSame(['vendor.module4', 'option2', 'value2', false], Option::getMockedParams('set', 0));
    }

    public function testRestoreFilteredNoBackUpSite(): void
    {
        $fileName = __DIR__ . '/fake/options_fake.php';
        $this->clearMockFileExists([$fileName => true]);
        Option::cleanMockData('set');
        $dumper = new OptionsDump('/');
        $dumper->restore('vendor.module4', $fileName, ['option1'], 's1', false);
        self::assertSame(1, Option::getMockedCounter('set'));
        self::assertSame(['vendor.module4', 'option2', 'value2', 's1'], Option::getMockedParams('set', 0));
    }

    public function testRestoreFilteredBackUp(): void
    {
        $options = ['option1' => 'value1', 'option2' => 'value2'];
        Option::cleanMockData('set');
        $fileName = __DIR__ . '/fake/options_fake.php';
        $this->clearMockFileExists([$fileName => true]);
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: ['option1' => 'value1']));
        $file = new \stdClass();
        $bkpFileName = '/vendor.module4-' . date('YmdHis') . '-bkp.php';
        $this->clearMockFOpen([$bkpFileName => $file]);
        $this->clearMockFWrite();
        $this->clearMockFClose();

        $dumper = new OptionsDump('/');
        $dumper->restore('vendor.module4', $fileName, ['option1']);
        self::assertSame(1, Option::getMockedCounter('set'));
        self::assertSame(['vendor.module4', 'option2', 'value2', false], Option::getMockedParams('set', 0));

        self::assertSame(1, self::$mockFileExistsCount);
        self::assertSame($fileName, self::$mockFileExistsParam[0]);
        self::assertSame(1, self::$mockFOpenCount);
        self::assertSame(1, self::$mockFCloseCount);
    }

    public function testRestoreFilteredBackUp2(): void
    {
        $fileName = __DIR__ . '/fake/options_fake.php';
        $this->clearMockFileExists([$fileName => true]);
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: ['option1' => 'value1']));
        $file = new \stdClass();
        $bkpFileName = '/vendor.module-' . date('YmdHis') . '-bkp.php';
        $this->clearMockFOpen([$bkpFileName => $file]);
        $this->clearMockFWrite();
        $this->clearMockFClose();
        $settings = TestModuleSettings::getInstance();

        $dumper = new OptionsDump('/');
        $dumper->restoreSettings($settings, $fileName, ['option2']);
        self::assertSame(1, $settings->saveFromArrayCallCount);
        self::assertSame(['option1' => 'value1'], $settings->saveFromArrayParams[0]);
        self::assertSame(1, self::$mockFileExistsCount);
        self::assertSame($fileName, self::$mockFileExistsParam[0]);
        self::assertSame(1, self::$mockFOpenCount);
        self::assertSame(1, self::$mockFCloseCount);
    }
}

class TestModuleSettings extends ModuleSettings
{
    public int $saveFromArrayCallCount = 0;
    public array $saveFromArrayParams = [];

    protected function __construct()
    {
        parent::__construct('vendor.module', false, 's1');
    }

    public static function getInstance(bool $sendThrow = true): static
    {
        return new static();
    }

    protected function initNormalizers(): void
    {
        // do nothing
    }

    public function saveFromArray(array $data): void
    {
        $this->saveFromArrayParams[$this->saveFromArrayCallCount] = $data;
        ++$this->saveFromArrayCallCount;
    }
}
