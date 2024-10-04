# Laravel interface for using Tableau APIs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mbingham/laravel-tableau.svg?style=flat-square)](https://packagist.org/packages/mbingham/laravel-tableau)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mbingham/laravel-tableau/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mbingham/laravel-tableau/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mbingham/laravel-tableau/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mbingham/laravel-tableau/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mbingham/laravel-tableau.svg?style=flat-square)](https://packagist.org/packages/mbingham/laravel-tableau)

A Laravel package that allows you to interact with Tableau Server and Tableau Cloud's APIs. This package provides a simple and flexible interface for interacting with the following APIs:

- [REST API](https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api.htm)
- [Metadata API](https://help.tableau.com/current/api/metadata_api/en-us/index.html) *not yet implemented*
- [VizQL Data Service](https://help.tableau.com/current/api/vizql-data-service/en-us/reference/index.html) *not yet implemented*

## Requirements

- Laravel 10.x or higher
- PHP 8.2 or higher
- Composer

## Installation

You can install the package via composer:

```bash
composer require interworks/laravel-tableau
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-tableau-config"
```

This is the contents of the published config file:

```php
return [
    'server_url' => env('TABLEAU_SERVER_URL', 'https://your-tableau-server.com'),
    'api_version' => env('TABLEAU_API_VERSION', '3.11'),
    'credentials' => [
        'username' => env('TABLEAU_USERNAME', ''),
        'password' => env('TABLEAU_PASSWORD', ''),
        'site_id' => env('TABLEAU_SITE_ID', '')
    ],
    'token_name' => env('TABLEAU_TOKEN_NAME', ''),
];
```

## Usage

Initialize the API

```php
use InterWorks\Tableau\TableauApi;

$tableau = new TableauApi();
```

Alternatively, you can override configuration dynamically if needed:

```php
Config::set('tableau.server_url', 'https://my-new-server-url.com');
Config::set('tableau.product_version', '3.10');
$tableau = new TableauApi();
```

### Authentication

You don't need to manually authenticate. The `TableauApi` class automatically manages authentication tokens and reuses
them as long as they remain valid.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributing

Feel free to fork this repository and submit pull requests. Make sure to add tests for new features or changes.

1. Fork the repository.
2. Create your feature branch (git checkout -b feature/my-new-feature).
3. Commit your changes (git commit -am 'Add some feature').
4. Push to the branch (git push origin feature/my-new-feature).
5. Open a pull request.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [mbingham](https://github.com/mbingham)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
