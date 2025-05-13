<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Settings;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use PHPUnit\Framework\TestCase;
use Vasoft\Core\Settings\ModuleSettings;
use Vasoft\Core\Settings\Exceptions\RequiredOptionException;
use Vasoft\Core\Settings\Normalizers\Normalizer;
use Vasoft\MockBuilder\Mocker\MockDefinition;

/**
 * @coversDefaultClass \Vasoft\Core\Settings\ModuleSettings
 *
 * @internal
 */
final class ModuleSettingsTest extends TestCase
{
    public function testSaveFromArray(): void
    {
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: []));
        Option::cleanMockData('set');

        $settings = FirstModuleSettings::getInstance(siteId: __METHOD__);
        $settings->saveFromArray([
            'EXAMPLE_STRING' => 'New value',
            'EXAMPLE_INT' => 222,
            'UNKNOWN_PROP' => 'Unknown',
        ]);
        self::assertSame(
            ['tests.module1', 'EXAMPLE_STRING', 'New value', __METHOD__],
            Option::getMockedParams('set', 0),
        );
        self::assertSame(
            ['tests.module1', 'EXAMPLE_INT', '222', __METHOD__],
            Option::getMockedParams('set', 1),
        );
        self::assertSame(2, Option::getMockedCounter('set'));
        self::assertSame(2, Option::getMockedCounter('getForModule'), 'Mast reload module settings');
    }

    public function testMultiple(): void
    {
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: []));

        $settings = FirstModuleSettings::getInstance(false, __METHOD__);
        $settings2 = FirstModuleSettings::getInstance(false, __METHOD__);
        self::assertSame($settings, $settings2);
    }

    public function testCleanMultipleInstancesAndNotThrows(): void
    {
        Option::cleanMockData(
            'getForModule',
            defaultDefinition: new MockDefinition(result: ['EXAMPLE_STRING' => 'Example value', 'EXAMPLE_INT' => 123]),
        );
        Option::cleanMockData('delete');

        $definition1 = new MockDefinition(['REQUIRED_OPTION_EXCEPTION'], result: 'Name: "%s" Code: "%s"');
        $definition2 = new MockDefinition(['EXAMPLE_INT'], result: 'Example value');
        Loc::cleanMockData(
            'getMessage',
            [$definition1, $definition2],
            defaultDefinition: new MockDefinition(result: ''),
            namedMode: true,
        );

        $settings = FirstModuleSettings::getInstance(false, __METHOD__);
        $settings2 = FirstModuleSettings::getInstance(false, __METHOD__ . 'ext');
        self::assertSame(123, $settings->getExampleInt());
        self::assertSame(123, $settings2->getExampleInt());
        $settings->clean();
        self::assertSame(0, $settings->getExampleInt());
        self::assertSame(123, $settings2->getExampleInt());
        self::assertSame(2, Option::getMockedCounter('getForModule'));
        self::assertSame(1, Option::getMockedCounter('delete'));
    }

    public function testCleanAndThrows(): void
    {
        Option::cleanMockData(
            'getForModule',
            defaultDefinition: new MockDefinition(result: ['EXAMPLE_STRING' => 'Example value', 'EXAMPLE_INT' => 123]),
        );
        Option::cleanMockData('delete');

        $definition1 = new MockDefinition(['REQUIRED_OPTION_EXCEPTION'], result: 'Name: "%s" Code: "%s"');
        $definition2 = new MockDefinition(['EXAMPLE_INT'], result: 'Example value');
        Loc::cleanMockData(
            'getMessage',
            [$definition1, $definition2],
            defaultDefinition: new MockDefinition(result: ''),
            namedMode: true,
        );

        $settings = FirstModuleSettings::getInstance(siteId: __METHOD__);
        self::assertSame(123, $settings->getExampleInt());
        $settings->clean();
        $this->expectException(RequiredOptionException::class);
        $this->expectExceptionMessage('Name: "Example value" Code: "EXAMPLE_INT');
        $settings->getExampleInt();
    }

    public function testSetAndNormalize(): void
    {
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: []));
        Option::cleanMockData('set');

        $settings = FirstModuleSettings::getInstance(siteId: __METHOD__);
        $settings->set('EXAMPLE_STRING', ' Example value ');
        self::assertSame(
            ['tests.module1', 'EXAMPLE_STRING', 'Example value', __METHOD__],
            Option::getMockedParams('set', 0),
            'The option was not set.',
        );
        self::assertSame(1, Option::getMockedCounter('set'));
    }

    public function testWakeupMethodIsFinal(): void
    {
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: []));

        $reflection = new \ReflectionClass(objectOrClass: FirstModuleSettings::class);
        $wakeupMethod = $reflection->getMethod('__wakeup');

        self::assertTrue($wakeupMethod->isFinal(), 'Method __wakeup() is not final.');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot unserialize a singleton.');
        $wakeupMethod->invoke(FirstModuleSettings::getInstance(siteId: __METHOD__));
    }

    public function testCannotCloneInstance(): void
    {
        Option::cleanMockData('getForModule', defaultDefinition: new MockDefinition(result: []));

        $reflection = new \ReflectionClass(FirstModuleSettings::class);

        $cloneMethod = $reflection->getMethod('__clone');
        self::assertTrue($cloneMethod->isFinal(), 'Method __clone() is not final.');


        $instance = FirstModuleSettings::getInstance(siteId: __METHOD__);

        $cloneMethod->setAccessible(true);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot clone a singleton.');

        $cloneMethod->invoke($instance);
    }

    public function testConstructorIsPrivate(): void
    {
        $reflection = new \ReflectionClass(FirstModuleSettings::class);
        $constructor = $reflection->getConstructor();

        self::assertTrue($constructor->isProtected() || $constructor->isPrivate());
    }
}

class FirstModuleSettings extends ModuleSettings
{
    public const MODULE_ID = 'tests.module1';
    public const PROP_EXAMPLE_INT = 'EXAMPLE_INT';
    public const PROP_EXAMPLE_STRING = 'EXAMPLE_STRING';

    public static function getInstance(bool $sendThrow = true, string $siteId = ''): static
    {
        return self::initInstance(self::MODULE_ID, $sendThrow, $siteId);
    }

    protected function initNormalizers(): void
    {
        $this->normalizer = [
            self::PROP_EXAMPLE_STRING => [Normalizer::class, 'normalizeString'],
            self::PROP_EXAMPLE_INT => [Normalizer::class, 'normalizeNotZeroInt'],
        ];
    }

    /**
     * Геттер параметра у которого должно быть значение. Отключение исключений необходимо для страницы в админке,
     * когда есть возможность, что значения еще не задали.
     *
     * @throws RequiredOptionException
     */
    public function getExampleInt(): int|string
    {
        if (!array_key_exists(self::PROP_EXAMPLE_INT, $this->options)) {
            if ($this->sendThrow) {
                throw new RequiredOptionException(self::PROP_EXAMPLE_INT, $this->getOptionName(self::PROP_EXAMPLE_INT));
            }

            return 0;
        }

        return (int) $this->options[self::PROP_EXAMPLE_INT];
    }

    public function getExampleString(): string
    {
        return trim($this->options[self::PROP_EXAMPLE_STRING] ?? 'default value');
    }
}
