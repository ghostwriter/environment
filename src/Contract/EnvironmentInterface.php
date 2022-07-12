<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Contract;

use Countable;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;
use IteratorAggregate;
use Traversable;

/**
 * @extends IteratorAggregate<int,VariableInterface>
 * @extends Countable
 */
interface EnvironmentInterface extends Countable, IteratorAggregate
{
    /**
     * Get the number of variables.
     */
    public function count(): int;

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
     * Get all variables.
     *
     * @return Traversable<int,VariableInterface>
     */
    public function getIterator(): Traversable;

    /**
     * Get a server variable.
     *
     * @throws NotFoundException if variable $name does not exist or $default is not a string
     */
    public function getServerVariable(string $name, ?string $default = null): string;

    /**
     * Get an array copy of all server variables.
     *
     * @return array<string,string>
     */
    public function getServerVariables(): array;

    /**
     * Check if an environment variable exists.
     */
    public function hasEnvironmentVariable(string $name): bool;

    /**
     * Check if a server variable exists.
     */
    public function hasServerVariable(string $name): bool;

    /**
     * Set an environment variable.
     *
     * @throws InvalidNameException  if $name is empty, contains an equals sign `=` or the NULL-byte character `\0`
     * @throws InvalidValueException if $value starts/ends with whitespace character or contains the NULL-byte character `\0`
     */
    public function setEnvironmentVariable(string $name, string $value): void;

    /**
     * Set a server variable.
     *
     * @throws InvalidNameException  if $name is empty, contains an equals sign `=` or the NULL-byte character `\0`
     * @throws InvalidValueException if $value starts/ends with whitespace character or contains the NULL-byte character `\0`
     */
    public function setServerVariable(string $name, string $value): void;

    /**
     * Get an array copy of all variables.
     *
     * @return array<string,string>
     */
    public function toArray(): array;

    /**
     * Unset an environment variable.
     *
     * @throws NotFoundException if variable $name does not exist
     */
    public function unsetEnvironmentVariable(string $name): void;

    /**
     * Unset a server variable.
     *
     * @throws NotFoundException if variable $name does not exist
     */
    public function unsetServerVariable(string $name): void;
}
