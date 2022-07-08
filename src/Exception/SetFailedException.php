<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Exception;

use Ghostwriter\Environment\Contract\Exception\EnvironmentExceptionInterface;
use RuntimeException;

final class SetFailedException extends RuntimeException implements EnvironmentExceptionInterface
{
}
