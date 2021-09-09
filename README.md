# Nobitex Market PHP API

[![Packagist Version][icon-version]][link-version]
[![PHP from Packagist][icon-php-version]][link-php-version]
[![Tests Status][icon-workflow]][link-workflow]
[![Coverage Status][icon-coverage]][link-coverage]
[![License][icon-license]][link-license]
[![Twitter: nekofar][icon-twitter]][link-twitter]

> This is a PHP wrapper for the [Nobitex API][6].

## Installation

This wrapper relies on HTTPlug, which defines how HTTP message should be sent and received. 
You can use any library to send HTTP messages that implements [php-http/client-implementation][5].

```bash
composer require nekofar/nobitex:^1.0@dev
```

To install with cURL you may run the following command:

```bash
composer require nekofar/nobitex:^1.0@dev php-http/curl-client:^1.0
```

## Usage

Use your username and password to access your own account.

```php
use \Nekofar\Nobitex\Client;
use \Nekofar\Nobitex\Config;

$config = Config::doAuth('username', 'password')
$client = Client::create($config)

try {
    $profile = $client->getUserProfile();

    echo 'Email: ' . $profile->email . PHP_EOL;
    echo 'Last name: ' . $profile->lastName . PHP_EOL;
    echo 'First name: ' . $profile->firstName . PHP_EOL;

} catche (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```

## Contributing

The test suite is built using PHPUnit. Run the suite of unit tests by running
the `phpunit` command or this composer script.

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

---
[link-version]: https://packagist.org/packages/nekofar/nobitex
[link-php-version]: https://packagist.org/packages/nekofar/nobitex
[link-workflow]: https://github.com/nekofar/nobitex-api-php-client/actions/workflows/tests.yml
[link-coverage]: https://codecov.io/gh/nekofar/nobitex-api-php-client
[link-license]: https://github.com/nekofar/nobitex-api-php-client/blob/master/LICENSE
[link-twitter]: https://twitter.com/nekofar

[icon-version]: https://img.shields.io/packagist/v/nekofar/nobitex.svg
[icon-php-version]: https://img.shields.io/packagist/php-v/nekofar/nobitex.svg
[icon-workflow]: https://img.shields.io/github/workflow/status/nekofar/nobitex-api-php-client/Tests
[icon-coverage]: https://codecov.io/gh/nekofar/nobitex-api-php-client/graph/badge.svg
[icon-license]: https://img.shields.io/packagist/l/nekofar/nobitex.svg
[icon-twitter]: https://img.shields.io/twitter/follow/nekofar.svg?style=flat

[5]: https://packagist.org/providers/php-http/client-implementation
[6]: https://apidocs.nobitex.market/en/
