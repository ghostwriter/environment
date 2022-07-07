<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Ghostwriter\Environment\Contract\Environment\EnvironmentVariableInterface;
use Ghostwriter\Environment\Exception\InvalidEnvironmentVariableNameException;
use Ghostwriter\Environment\Exception\InvalidEnvironmentVariableValueException;
use function str_contains;
use function trim;

/**
 * @see EnvironmentVariableTest
 */
final class EnvironmentVariable implements EnvironmentVariableInterface
{
    public function __construct(
        private string $name,
        private string $value
    ) {
        $this->assertValidEnvironmentVariableName($this->name);
        $this->assertValidEnvironmentVariableValue($this->value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidEnvironmentVariableName(string $name): void
    {
        $trimmed = trim($name);
        if (
            '' === $trimmed ||
            $name !== $trimmed ||
            str_contains($name, '=') ||
            str_contains($name, "\0")
        ) {
            throw new InvalidEnvironmentVariableNameException();
        }
    }

    private function assertValidEnvironmentVariableValue(string $value): void
    {
        $trimmed = trim($value);
        if (
            '' === $trimmed ||
            $value !== $trimmed ||
            str_contains($value, "\0")
        ) {
            throw new InvalidEnvironmentVariableValueException();
        }
    }
}
