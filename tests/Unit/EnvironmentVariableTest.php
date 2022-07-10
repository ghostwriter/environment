<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Tests\Unit;

use Ghostwriter\Environment\Contract\VariableInterface;
use Ghostwriter\Environment\EnvironmentVariable;

/**
 * @coversDefaultClass \Ghostwriter\Environment\EnvironmentVariable
 *
 * @internal
 *
 * @small
 */
final class EnvironmentVariableTest extends AbstractVariableTest
{
    protected function createVariable(string $name, string $value): VariableInterface
    {
        return new EnvironmentVariable($name, $value);
    }
}
