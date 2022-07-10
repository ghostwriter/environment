<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Contract;

use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;
use Ghostwriter\Environment\Exception\SetFailedException;
use Ghostwriter\Environment\Exception\UnsetFailedException;

/**
 * @extends EnvironmentInterface
 */
interface EnvironmentVariablesInterface
{
    /**
     * Get an environment variable.
     *
     * @throws NotFoundException if variable $name does not exist or $default is not a string
     */
    public function getEnvironmentVariable(string $name, ?string $default = null): string;

    /**
     * Check if an environment variable exists.
     */
    public function hasEnvironmentVariable(string $name): bool;

    /**
     * Set an environment variable.
     *
     * @throws SetFailedException
     * @throws InvalidNameException  if $name is empty, contains an equals sign `=` or the NULL-byte character `\0`
     * @throws InvalidValueException if $value starts/ends with whitespace character or contains the NULL-byte character `\0`
     */
    public function setEnvironmentVariable(string $name, string $value): void;

    /**
     * Unset an environment variable.
     *
     * @throws UnsetFailedException
     */
    public function unsetEnvironmentVariable(string $name): void;
}
