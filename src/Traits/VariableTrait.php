<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Traits;

use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use function str_contains;
use function trim;

trait VariableTrait
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

    /** @return array<string,string> */
    public function toArray(): array
    {
        return [
            $this->name => $this->value,
        ];
    }

    private function assertValidName(string $name): void
    {
        $trimmedName = trim($name);
        if ('' === $trimmedName) {
            throw new InvalidNameException($name);
        }

        if ($name !== $trimmedName) {
            throw new InvalidNameException($name);
        }

        if (str_contains($name, '=')) {
            throw new InvalidNameException($name);
        }

        if (str_contains($name, "\0")) {
            throw new InvalidNameException($name);
        }
    }

    private function assertValidValue(string $value): void
    {
        $trimmedValue = trim($value);
        if ($value !== $trimmedValue) {
            throw new InvalidValueException($value);
        }

        if (str_contains($value, "\0")) {
            throw new InvalidValueException($value);
        }
    }
}
