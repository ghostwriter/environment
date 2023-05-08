# Environment

[![Compliance](https://github.com/ghostwriter/environment/actions/workflows/compliance.yml/badge.svg)](https://github.com/ghostwriter/environment/actions/workflows/compliance.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/ghostwriter/environment?color=8892bf)](https://www.php.net/supported-versions)
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

$environment->count(); // int
$environment->toArray(); // array<string,string>
$environment->getIterator(); // Traversable<int,VariableInterface>

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
