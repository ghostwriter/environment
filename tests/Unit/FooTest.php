<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Tests\Unit;

use Ghostwriter\Environment\Foo;

/**
 * @coversDefaultClass \Ghostwriter\Environment\Foo
 *
 * @internal
 *
 * @small
 */
final class FooTest extends AbstractTestCase
{
    /** @covers \Ghostwriter\Environment\Foo::test */
    public function test(): void
    {
        self::assertTrue((new Foo())->test());
    }
}
