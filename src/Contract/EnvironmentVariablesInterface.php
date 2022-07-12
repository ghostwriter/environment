<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Contract;

use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;

interface EnvironmentVariablesInterface
{
    /**
     * Get an environment variable.
     *
     * @throws NotFoundException if variable $name does not exist or $default is not a string
     */
    public function getEnvironmentVariable(string $name, ?string $default = null): string;

    /**
     * Get an array copy of all environment variables.
     *
     * @return array<string,string>
     */
    public function getEnvironmentVariables(): array;

    /**
     * Check if an environment variable exists.
     */
    public function hasEnvironmentVariable(string $name): bool;

    /**
     * Set an environment variable.
     *
     * @throws InvalidNameException  if $name is empty, contains an equals sign `=` or the NULL-byte character `\0`
     * @throws InvalidValueException if $value starts/ends with whitespace character or contains the NULL-byte character `\0`
     */
    public function setEnvironmentVariable(string $name, string $value): void;

    /**
     * Unset an environment variable.
     *
     * @throws NotFoundException if variable $name does not exist
     */
    public function unsetEnvironmentVariable(string $name): void;
}
