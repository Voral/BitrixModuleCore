<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Entities\Fields;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Vasoft\Core\Settings\Entities\Fields\HtmlField
 *
 * @internal
 */
final class HtmlFieldTest extends TestCase
{
    public function testRenderInput(): void
    {
        $field = new HtmlField(static fn() => '<div>HTML</div>');
        self::assertSame('', $field->renderInput(), 'HtmlField::renderInput() returns empty string');
    }

    public function testRender(): void
    {
        $field = new HtmlField(static fn() => '<div>HTML</div>');
        self::assertSame('<tr><td colspan="2"><div>HTML</div></td></tr>', $field->render());
    }

    public function testAutoincrementCode(): void
    {
        $field1 = new HtmlField(static fn() => '<div>HTML</div>');
        $field2 = new HtmlField(static fn() => '<div>HTML</div>');
        self::assertStringStartsWith('HTML_', $field1->code);
        self::assertStringStartsWith('HTML_', $field2->code);
        self::assertNotSame($field1->code, $field2->code);
        self::assertSame('', $field2->title);
    }
}
