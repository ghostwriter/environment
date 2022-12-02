<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Ghostwriter\Environment\Contract\ServerVariableInterface;
use Ghostwriter\Environment\Traits\VariableTrait;

/**
 * @see \Ghostwriter\Environment\Tests\Unit\ServerVariableTest
 */
final class ServerVariable implements ServerVariableInterface
{
    use VariableTrait;
}
