<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Ghostwriter\Environment\Contract\VariableInterface;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use function str_contains;
use function trim;

/**
 * @see VariableTest
 */
final class Variable implements VariableInterface
{
    /**
     * @param string $name
     * @param string $value
     *
     * @throws InvalidNameException  if $name is empty, contains an equals sign `=`,
     *                               or the NULL-byte character `\0`
     * @throws InvalidValueException if $value is empty or contains the NULL-byte character `\0`
     */
    public function __construct(
        private string $name,
        private string $value
    ) {
        $this->assertValidVariableName($this->name);
        $this->assertValidVariableValue($this->value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidVariableName(string $name): void
    {
        $trimmed = trim($name);
        if (
            '' === $trimmed ||
            $name !== $trimmed ||
            str_contains($name, '=') ||
            str_contains($name, "\0")
        ) {
            throw new InvalidNameException();
        }
    }

    private function assertValidVariableValue(string $value): void
    {
        $trimmed = trim($value);
        if (
            '' === $trimmed ||
            $value !== $trimmed ||
            str_contains($value, "\0")
        ) {
            throw new InvalidValueException();
        }
    }
}
