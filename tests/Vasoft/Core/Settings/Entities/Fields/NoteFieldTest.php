<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Entities\Fields;

use Vasoft\MockBuilder\Mocker\MockDefinition;
use Vasoft\MockBuilder\Mocker\MockFunctions;
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
        MockFunctions::cleanMockData('BeginNote', defaultDefinition: new MockDefinition(result: '<note>'));
        MockFunctions::cleanMockData('EndNote', defaultDefinition: new MockDefinition(result: '</note>'));
        $field = new NoteField(static fn() => '<div>HTML</div>');
        self::assertSame('<tr><td colspan="2"><note><div>HTML</div></note></td></tr>', $field->render());
        self::assertSame(1, MockFunctions::getMockedCounter('BeginNote'));
        self::assertSame(1, MockFunctions::getMockedCounter('EndNote'));
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
