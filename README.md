# Nobitex Market PHP API

[![Travis (.com) branch](https://img.shields.io/travis/com/nekofar/nobitex-api-php/master.svg)][3]
[![Codecov](https://img.shields.io/codecov/c/gh/nekofar/nobitex-api-php.svg)][4]
[![Packagist](https://img.shields.io/packagist/l/nekofar/nobitex.svg)][2]
[![Packagist Version](https://img.shields.io/packagist/v/nekofar/nobitex.svg)][1]
[![PHP from Packagist](https://img.shields.io/packagist/php-v/nekofar/nobitex.svg)][1]

## Installation

You can install the package via composer from [Packagist][1]:

```bash
composer require php-http/curl-client:~1.0 nekofar/nobitex:~1.0
```

## Usage

### Authentication
Use your username and password to access your own account.

```php
use \Nekofar\Nobitex\Client;
use \Nekofar\Nobitex\Config;

$config = Config::doAuth('username', 'password')
$client = Client::create($config)
```

### Market Data

#### Market Orders

```php
$orders = $client->getMarketOrders();
```

#### Market Trades

```php
$trades = $client->getMarketTrades([
    "srcCurrency" => "btc",
    "dstCurrency" => "rls"
]);
```

### User Info

#### User Profile

```php
$profile = $client->getUserProfile();
```

#### Login Attempts

```php
$attempts = $client->getUserLoginAttempts();
```

#### Referral Code

```php
$referralCode = $client->getUserReferralCode();
```

#### User Limitations 

```php
$limitations = $client->getUserLimitations();
```

#### Add Bank Card

```php
$status = $client->addUserCard([
    "number" => "5041721011111111",
    "bank" => "Resalat"
]);
```

#### Add Bank Account

```php
$status = $client->addUserAccount([
    "number" => "5041721011111111",
    "bank" => "Resalat",
    "shaba" => "IR111111111111111111111111",
]);
```

#### User Wallets

```php
$wallets = $client->getUserWallets();
```

#### User Wallet Balance

```php
$balance = $client->getUserWalletBalance(['currency' => 'ltc']);
```

#### User Wallet Transactions

```php
$transactions = $client->getUserWalletTransactions(['wallet' => 123456]);
```

#### User Wallet Deposits

```php
$deposits = $client->getUserWalletDeposits(['wallet' => 123456]);
```

#### User Wallet Withdraws

```php
$withdraws = $client->getUserWalletWithdraws(['wallet' => 123456]);
```

#### Generate User Wallet Address

```php
$address = $client->genUserWalletAddress(['wallet' => 123456]);
```

## Contributing and testing

The test suite is built using PHPUnit. Run the suite of unit tests by running
the `phpunit` command or composer script.

```bash
composer test
```

---
[1]: https://packagist.org/packages/nekofar/nobitex
[2]: https://github.com/nekofar/nobitex-api-php/blob/master/LICENSE
[3]: https://travis-ci.com/nekofar/nobitex-api-php
[4]: https://codecov.io/gh/nekofar/nobitex-api-php