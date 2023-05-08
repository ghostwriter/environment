<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Countable;
use Generator;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<non-empty-string,non-empty-string>
 */
interface EnvironmentVariablesInterface extends Countable, IteratorAggregate
{
    /**
     * Get the number of variables.
     */
    public function count(): int;

    /**
     * Get an environment variable.
     *
     * @param non-empty-string      $name
     * @param null|non-empty-string $default
     *
     * @throws NotFoundException if variable $name does not exist or $default is not a string
     *
     * @return non-empty-string
     *
     */
    public function get(string $name, string|null $default = null): string;

    /**
     * Get all variables.
     *
     * @return Generator<non-empty-string,non-empty-string>
     */
    public function getIterator(): Generator;

    /**
     * Check if a variable exists.
     *
     * @param non-empty-string $name
     */
    public function has(string $name): bool;

    /**
     * Set a variable.
     *
     * @param non-empty-string $name
     * @param non-empty-string $value
     *
     * @throws InvalidNameException  if $name is empty, contains an equals sign `=` or the NULL-byte character `\0`
     * @throws InvalidValueException if $value starts/ends with whitespace character or contains the NULL-byte character `\0`
     */
    public function set(string $name, string $value): void;

    /**
     * Get an array copy of all variables.
     *
     * @return non-empty-array<non-empty-string,non-empty-string>
     */
    public function toArray(): array;

    /**
     * Unset a variable.
     *
     * @param non-empty-string $name
     *
     * @throws NotFoundException if variable $name does not exist
     */
    public function unset(string $name): void;
}
