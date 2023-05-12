# Environment

[![Compliance](https://github.com/ghostwriter/environment/actions/workflows/compliance.yml/badge.svg)](https://github.com/ghostwriter/environment/actions/workflows/compliance.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/ghostwriter/environment?color=8892bf)](https://www.php.net/supported-versions)
[![Mutation Coverage](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fghostwriter%2Fenvironment%2Fmain)](https://dashboard.stryker-mutator.io/reports/github.com/ghostwriter/environment/main)
[![Code Coverage](https://codecov.io/gh/ghostwriter/environment/branch/main/graph/badge.svg)](https://codecov.io/gh/ghostwriter/environment)
[![Type Coverage](https://shepherd.dev/github/ghostwriter/environment/coverage.svg)](https://shepherd.dev/github/ghostwriter/environment)
[![Latest Version on Packagist](https://badgen.net/packagist/v/ghostwriter/environment)](https://packagist.org/packages/ghostwriter/environment)
[![Downloads](https://badgen.net/packagist/dt/ghostwriter/environment?color=blue)](https://packagist.org/packages/ghostwriter/environment)

Provides Environment Variables derived from `$_ENV` and `$_SERVER` super-globals

## Installation

You can install the package via composer:

``` bash
composer require ghostwriter/environment
```

## Usage

```php
$environment = new \Ghostwriter\Environment\EnvironmentVariables();

$environment->has('APP_ENV'); // false
$environment->get('APP_ENV', 'dev'); // dev
$environment->get('APP_ENV'); // throws NotFoundException
$environment->set('APP_ENV', 'production');
$environment->has('APP_ENV'); // true
$environment->get('APP_ENV'); // production
$environment->unset('APP_ENV');

$environment->set('APP_KEY', 'secrete');
$environment->has('APP_KEY'); // true
$environment->get('APP_KEY'); // secrete
$environment->unset('APP_KEY');
$environment->has('APP_KEY'); // false
$environment->get('APP_KEY', 'fallback-value'); // fallback-value
$environment->get('APP_KEY'); // throws NotFoundException
```

## API

```php
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
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG.md](./CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email `nathanael.esayeas@protonmail.com` instead of using the issue tracker.

## Sponsors

[[`Become a GitHub Sponsor`](https://github.com/sponsors/ghostwriter)]

## Credits

- [Nathanael Esayeas](https://github.com/ghostwriter)
- [All Contributors](https://github.com/ghostwriter/environment/contributors)

## License

The BSD-3-Clause. Please see [License File](./LICENSE) for more information.
