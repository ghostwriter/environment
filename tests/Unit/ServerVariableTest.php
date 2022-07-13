<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Tests\Unit;

use Ghostwriter\Environment\Contract\VariableInterface;
use Ghostwriter\Environment\ServerVariable;

/**
 * @coversDefaultClass \Ghostwriter\Environment\ServerVariable
 *
 * @internal
 *
 * @small
 */
final class ServerVariableTest extends AbstractVariableTest
{
    protected function createVariable(string $name, string $value): VariableInterface
    {
        return new ServerVariable($name, $value);
    }
}
