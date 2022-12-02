<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Ghostwriter\Environment\Contract\EnvironmentVariableInterface;
use Ghostwriter\Environment\Traits\VariableTrait;

/**
 * @see \Ghostwriter\Environment\Tests\Unit\EnvironmentVariableTest
 */
final class EnvironmentVariable implements EnvironmentVariableInterface
{
    use VariableTrait;
}
