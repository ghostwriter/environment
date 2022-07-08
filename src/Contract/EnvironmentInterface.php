<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Contract;

use Countable;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;
use Ghostwriter\Environment\Exception\SetFailedException;
use Ghostwriter\Environment\Exception\UnsetFailedException;
use IteratorAggregate;
use Traversable;

/**
 * @extends IteratorAggregate<int,VariableInterface>
 * @extends Countable
 */
interface EnvironmentInterface extends Countable, IteratorAggregate
{
    /**
     * Get the number of environment variables.
     */
    public function count(): int;

    /**
     * Get all environment variables.
     *
     * @return Traversable<int,VariableInterface>
     */
    public function getIterator(): Traversable;

    /**
     * Get an environment variable.
     *
     * @throws NotFoundException
     */
    public function getVariable(string $name): string;

    /**
     * Check if an environment variable exists.
     */
    public function hasVariable(string $name): bool;

    /**
     * Set an environment variable.
     *
     * @throws SetFailedException
     * @throws InvalidNameException  if $name is empty, contains an equals sign `=` or the NULL-byte character `\0`
     * @throws InvalidValueException if $value is empty or contains the NULL-byte character `\0`
     */
    public function setVariable(string $name, string $value): void;

    /**
     * Get an array copy of all the environment variables.
     *
     * @return array<string,string>
     */
    public function toArray(): array;

    /**
     * Unset an environment variable.
     *
     * @throws UnsetFailedException
     */
    public function unsetVariable(string $name): void;
}
