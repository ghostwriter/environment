<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Exception;

use RuntimeException;

final class InvalidNameException extends RuntimeException implements EnvironmentExceptionInterface
{
}
