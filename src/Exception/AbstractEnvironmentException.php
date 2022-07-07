<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Exception;

use Ghostwriter\Environment\Contract\Exception\EnvironmentExceptionInterface;
use RuntimeException;

abstract class AbstractEnvironmentException extends RuntimeException implements EnvironmentExceptionInterface
{
}
