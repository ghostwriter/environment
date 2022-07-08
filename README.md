# Environment

[![Compliance](https://github.com/ghostwriter/environment/actions/workflows/compliance.yml/badge.svg)](https://github.com/ghostwriter/environment/actions/workflows/continuous-integration.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/ghostwriter/environment?color=8892bf)](https://www.php.net/supported-versions)
[![Type Coverage](https://shepherd.dev/github/ghostwriter/environment/coverage.svg)](https://shepherd.dev/github/ghostwriter/environment)
[![Latest Version on Packagist](https://badgen.net/packagist/v/ghostwriter/environment)](https://packagist.org/packages/ghostwriter/environment)
[![Downloads](https://badgen.net/packagist/dt/ghostwriter/environment?color=blue)](https://packagist.org/packages/ghostwriter/environment)

Environment Variables for PHP

## Installation

You can install the package via composer:

``` bash
composer require ghostwriter/environment
```

## Usage

```php
$environment = new \Ghostwriter\Environment\Environment();

$environment->count();
$environment->toArray();
$environment->getIterator();

$environment->hasVariable('APP_ENV'); // false
$environment->getVariable('APP_ENV', 'dev'); // dev
$environment->getVariable('APP_ENV'); // throws NotFoundException
$environment->setVariable('APP_ENV', 'production');
$environment->hasVariable('APP_ENV'); // true
$environment->getVariable('APP_ENV'); // production

$environment->setVariable('APP_KEY', 'secrete');
$environment->hasVariable('APP_KEY'); // true
$environment->getVariable('APP_KEY'); // secrete
$environment->unsetVariable('APP_KEY');
$environment->hasVariable('APP_KEY'); // false
$environment->getVariable('APP_KEY', 'fallback-value'); // fallback-value
$environment->getVariable('APP_KEY'); // throws NotFoundException
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

[![ghostwriter's GitHub Sponsors](https://img.shields.io/github/sponsors/ghostwriter?label=Sponsors&logo=GitHub%20Sponsors)](https://github.com/sponsors/ghostwriter)

Maintaining open source software is a thankless, time-consuming job.

Sponsorships are one of the best ways to contribute to the long-term sustainability of an open-source licensed project.

Please consider giving back, to fund the continued development of `ghostwriter/environment`, by sponsoring me here on GitHub.

[[Become a GitHub Sponsor](https://github.com/sponsors/ghostwriter)]

### For Developers

Please consider helping your company become a GitHub Sponsor, to support the open-source licensed project that runs your business.

## Credits

- [Nathanael Esayeas](https://github.com/ghostwriter)
- [All Contributors](https://github.com/ghostwriter/environment/contributors)

## License

The BSD-3-Clause. Please see [License File](./LICENSE) for more information.
