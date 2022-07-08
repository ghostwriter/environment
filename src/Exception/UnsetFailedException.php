<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Exception;

use Ghostwriter\Environment\Contract\Exception\EnvironmentExceptionInterface;
use RuntimeException;

final class UnsetFailedException extends RuntimeException implements EnvironmentExceptionInterface
{
}
