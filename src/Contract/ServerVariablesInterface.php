<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Contract;

use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;
use Ghostwriter\Environment\Exception\SetFailedException;
use Ghostwriter\Environment\Exception\UnsetFailedException;

interface ServerVariablesInterface
{
    /**
     * Get a server variable.
     *
     * @throws NotFoundException if variable $name does not exist or $default is not a string
     */
    public function getServerVariable(string $name, ?string $default = null): string;

    /**
     * Check if a server variable exists.
     */
    public function hasServerVariable(string $name): bool;

    /**
     * Set a server variable.
     *
     * @throws SetFailedException
     * @throws InvalidNameException  if $name is empty, contains an equals sign `=` or the NULL-byte character `\0`
     * @throws InvalidValueException if $value starts/ends with whitespace character or contains the NULL-byte character `\0`
     */
    public function setServerVariable(string $name, string $value): void;

    /**
     * Unset a server variable.
     *
     * @throws UnsetFailedException
     */
    public function unsetServerVariable(string $name): void;
}
