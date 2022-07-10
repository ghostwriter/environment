<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Ghostwriter\Environment\Contract\VariableInterface;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use function str_contains;
use function trim;

/**
 * @see \Ghostwriter\Environment\Tests\Unit\AbstractVariableTest
 */
abstract class AbstractVariable implements VariableInterface
{
    /**
     * @throws InvalidNameException  if $name is empty, contains an equals sign `=`,
     *                               or the NULL-byte character `\0`
     * @throws InvalidValueException if $value starts/ends with whitespace character or contains the NULL-byte character `\0`
     */
    final public function __construct(
        private string $name,
        private string $value
    ) {
        $this->assertValidName($this->name);
        $this->assertValidValue($this->value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidName(string $name): void
    {
        $trimmed = trim($name);
        if (
            '' === $trimmed ||
            $name !== $trimmed ||
            str_contains($name, '=') ||
            str_contains($name, "\0")
        ) {
            throw new InvalidNameException($name);
        }
    }

    private function assertValidValue(string $value): void
    {
        if ($value !== trim($value) || str_contains($value, "\0")) {
            throw new InvalidValueException($value);
        }
    }
}
