<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Entities\Fields;

use Bitrix\Mocker\FunctionMocker;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Vasoft\Core\Settings\Entities\Fields\NoteField
 *
 * @internal
 */
final class NoteFieldTest extends TestCase
{
    public function testRenderInput(): void
    {
        $field = new NoteField(static fn() => '<div>HTML</div>');
        self::assertSame('', $field->renderInput(), 'HtmlField::renderInput() returns empty string');
    }

    public function testRender(): void
    {
        FunctionMocker::cleanMockData('BeginNote', defaultResult: '<note>');
        FunctionMocker::cleanMockData('EndNote', defaultResult: '</note>');
        $field = new NoteField(static fn() => '<div>HTML</div>');
        self::assertSame('<tr><td colspan="2"><note><div>HTML</div></note></td></tr>', $field->render());
        self::assertSame(1, FunctionMocker::getMockedCounter('BeginNote'));
        self::assertSame(1, FunctionMocker::getMockedCounter('EndNote'));
    }

    public function testAutoincrementCode(): void
    {
        $field1 = new NoteField(static fn() => '<div>HTML</div>');
        $field2 = new NoteField(static fn() => '<div>HTML</div>');
        self::assertStringStartsWith('NOTE_', $field1->code);
        self::assertStringStartsWith('NOTE_', $field2->code);
        self::assertNotSame($field1->code, $field2->code);
        self::assertSame('', $field2->title);
    }
}
