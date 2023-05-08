<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Exception;

use RuntimeException;

final class InvalidValueException extends RuntimeException implements EnvironmentExceptionInterface
{
}
