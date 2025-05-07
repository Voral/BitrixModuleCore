<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Entities\Fields;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Objectify\Collection;
use Bitrix\Main\ORM\Query\Query;
use Vasoft\MockBuilder\Mocker\MockDefinition;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Settings\Entities\Fields\UserGroupsField
 */
final class UserGroupsFieldTest extends TestCase
{
    public function testRenderInputEmpty(): void
    {
        Loc::cleanMockData('getMessage', defaultDefinition: new MockDefinition(result: 'Not selected'));
        $elements = [];
        $collection = $this->getCollection($elements);

        $queryMock = self::createMock(Query::class);
        DataManager::cleanMockData('query', defaultDefinition: new MockDefinition(result: $queryMock));
        $queryMock->expects(self::once())
            ->method('addOrder')
            ->with('C_SORT')
            ->willReturnSelf();

        $addSelectParams = [];
        $queryMock->expects(self::exactly(2))
            ->method('addSelect')
            ->willReturnCallback(static function ($param) use (&$calls, $queryMock, &$addSelectParams) {
                $addSelectParams[] = $param;

                return $queryMock;
            });

        $queryMock->expects(self::once())
            ->method('fetchCollection')
            ->willReturn($collection);

        $field = new UserGroupsField('test', 'Select it', static fn() => '');
        self::assertSame(
            '<select name="test" ><option value="0" >Not selected</option></select>',
            $field->renderInput(),
        );
        self::assertCount(2, $addSelectParams, 'Should be two selects');
        $addSelectParams = array_flip($addSelectParams);
        self::assertArrayHasKey('ID', $addSelectParams, 'Field ID should be added');
        self::assertArrayHasKey('NAME', $addSelectParams, 'Field NAME should be added');
        self::assertSame('FIELD_USER_GROUP_EMPTY', Loc::getMockedParams('getMessage', 0)[0]);
    }

    public function testRenderNotSelected(): void
    {
        Loc::cleanMockData('getMessage', defaultDefinition: new MockDefinition(result: 'Not selected'));
        $elements = [new TestGroup(), new TestGroup()];
        $collection = $this->getCollection($elements);

        $queryMock = self::createMock(Query::class);
        DataManager::cleanMockData('query', defaultDefinition: new MockDefinition(result: $queryMock));
        $queryMock->expects(self::once())
            ->method('addOrder')
            ->with('C_SORT')
            ->willReturnSelf();

        $addSelectParams = [];
        $queryMock->expects(self::exactly(2))
            ->method('addSelect')
            ->willReturnCallback(static function ($param) use (&$calls, $queryMock, &$addSelectParams) {
                $addSelectParams[] = $param;

                return $queryMock;
            });

        $queryMock->expects(self::once())
            ->method('fetchCollection')
            ->willReturn($collection);

        $field = new UserGroupsField('test', 'Select it', static fn() => '');
        self::assertSame(
            '<select name="test" ><option value="0" >Not selected</option><option value="1" >Test 1</option><option value="2" >Test 2</option></select>',
            $field->renderInput(),
        );
    }

    public function testRenderSelected(): void
    {
        Loc::cleanMockData('getMessage', defaultDefinition: new MockDefinition(result: 'Not selected'));
        $elements = [new TestGroup(), new TestGroup()];
        $collection = $this->getCollection($elements);

        $queryMock = self::createMock(Query::class);
        DataManager::cleanMockData('query', defaultDefinition: new MockDefinition(result: $queryMock));
        $queryMock->expects(self::once())
            ->method('addOrder')
            ->with('C_SORT')
            ->willReturnSelf();

        $addSelectParams = [];
        $queryMock->expects(self::exactly(2))
            ->method('addSelect')
            ->willReturnCallback(static function ($param) use (&$calls, $queryMock, &$addSelectParams) {
                $addSelectParams[] = $param;

                return $queryMock;
            });

        $queryMock->expects(self::once())
            ->method('fetchCollection')
            ->willReturn($collection);

        $field = new UserGroupsField('test', 'Select it', static fn() => 1);
        self::assertSame(
            '<select name="test" ><option value="0" >Not selected</option><option value="1" selected>Test 1</option><option value="2" >Test 2</option></select>',
            $field->renderInput(),
        );
    }

    public function testRenderSelectedMultiple(): void
    {
        Loc::cleanMockData('getMessage', defaultDefinition: new MockDefinition(result: 'Not selected'));
        $elements = [new TestGroup(), new TestGroup()];
        $collection = $this->getCollection($elements);

        $queryMock = self::createMock(Query::class);
        DataManager::cleanMockData('query', defaultDefinition: new MockDefinition(result: $queryMock));
        $queryMock->expects(self::once())
            ->method('addOrder')
            ->with('C_SORT')
            ->willReturnSelf();

        $addSelectParams = [];
        $queryMock->expects(self::exactly(2))
            ->method('addSelect')
            ->willReturnCallback(static function ($param) use (&$calls, $queryMock, &$addSelectParams) {
                $addSelectParams[] = $param;

                return $queryMock;
            });

        $queryMock->expects(self::once())
            ->method('fetchCollection')
            ->willReturn($collection);

        $field = new UserGroupsField('groups', 'Select it', static fn() => [1, 2]);
        $field->configureMultiple();
        self::assertSame(
            '<select name="groups[]" multiple><option value="0" >Not selected</option><option value="1" selected>Test 1</option><option value="2" selected>Test 2</option></select>',
            $field->renderInput(),
        );
    }

    private function getCollection(array $elements): Collection
    {
        TestGroup::$count = 0;
        $collection = self::createMock(Collection::class);
        $currentIndex = 0;

        $collection->method('rewind')->willReturnCallback(static function () use (&$currentIndex): void {
            $currentIndex = 0;
        });

        $collection->method('current')->willReturnCallback(static function () use ($elements, &$currentIndex) {
            return $elements[$currentIndex];
        });

        $collection->method('key')->willReturnCallback(static function () use (&$currentIndex) {
            return $currentIndex;
        });

        $collection->method('next')->willReturnCallback(static function () use (&$currentIndex): void {
            ++$currentIndex;
        });

        $collection->method('valid')->willReturnCallback(static function () use ($elements, &$currentIndex) {
            return isset($elements[$currentIndex]);
        });

        return $collection;
    }
}

class TestGroup
{
    public static int $count = 0;
    protected int $id;

    public function __construct()
    {
        ++self::$count;
        $this->id = self::$count;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return 'Test ' . $this->id;
    }
}
