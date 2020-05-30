<p align="center">
<img src="https://ashallendesign.co.uk/images/custom/laravel-exchange-rates-logo.png" alt="Laravel Exchange Rates" width="600">
</p>

<p align="center">
<a href="https://packagist.org/packages/ashallendesign/laravel-exchange-rates"><img src="https://img.shields.io/packagist/v/ashallendesign/laravel-exchange-rates.svg?style=flat-square" alt="Latest Version on Packagist"></a>
<a href="https://travis-ci.org/ash-jc-allen/laravel-exchange-rates"><img src="https://img.shields.io/travis/ash-jc-allen/laravel-exchange-rates/master.svg?style=flat-square" alt="Build Status"></a>
<a href="https://packagist.org/packages/ashallendesign/laravel-exchange-rates"><img src="https://img.shields.io/packagist/dt/ashallendesign/laravel-exchange-rates.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ashallendesign/laravel-exchange-rates"><img src="https://img.shields.io/packagist/php-v/ashallendesign/laravel-exchange-rates?style=flat-square" alt="PHP from Packagist"></a>
<a href="https://github.com/ash-jc-allen/short-url/blob/master/LICENSE"><img src="https://img.shields.io/github/license/ash-jc-allen/laravel-exchange-rates?style=flat-square" alt="GitHub license"></a>
</p>

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
- [Usage](#usage)
    - [Methods](#methods)
        - [Available Currencies](#available-currencies)
        - [Exchange Rate](#exchange-rate)
        - [Exchange Rates Between Date Range](#exchange-rates-between-date-range)
        - [Convert Currencies](#convert-currencies)
        - [Convert Currencies Between Date Range](#convert-currencies-between-date-range)
    - [Facade](#facade)
    - [Examples](#examples)
    - [Caching](#caching)
    - [Supported Currencies](#supported-currencies)
- [Testing](#testing)
- [Security](#security)
- [Contribution](#contribution)
- [Credits](#credits)
- [Changelog](#changelog)
- [Upgrading](#upgrading)
- [License](#license)
    
## Overview

A simple Laravel package used for interacting with the [exchangeratesapi.io](http://exchangeratesapi.io) API. 'Laravel Exchange Rates'
allow you to get the latest or historical exchange rates and convert monetary values between different currencies.

## Installation

You can install the package via Composer:

```bash
composer require ashallendesign/laravel-exchange-rates
```

The package has been developed and tested to work with the following minimum requirements:

- PHP 7.2
- Laravel 5.8

## Usage

### Methods
#### Available Currencies
``` php
$exchangeRates = new ExchangeRate();
$exchangeRates->currencies();
```

#### Exchange Rate
```php
<?php
$exchangeRates = new ExchangeRate();
$exchangeRates->exchangeRate('GBP', 'EUR');
```
Note: If a Carbon date is passed as the third parameter, the exchange rate for that day will be returned (if valid).
If no date is passed, today's exchange rate will be used.

#### Exchange Rates Between Date Range
```php
$exchangeRates = new ExchangeRate();
$exchangeRates->exchangeRateBetweenDateRange('GBP', 'EUR', Carbon::now()->subWeek(), Carbon::now());
```

#### Convert Currencies
When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest
denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

```php
$exchangeRates = new ExchangeRate();
$exchangeRates->convert(100, 'GBP', 'EUR', Carbon::now());
```

Note: If a Carbon date is passed as the third parameter, the exchange rate for that day will be returned (if valid).
If no date is passed, today's exchange rate will be used.

#### Convert Currencies Between Date Range
When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest
denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

```php
$exchangeRates = new ExchangeRate();
$exchangeRates->convert(100, 'GBP', 'EUR', Carbon::now()->subWeek(), Carbon::now());
```

### Facade
If you prefer to use facades in Laravel, you can choose to use the provided ```ExchangeRate ``` facade instead of instantiating the ``` AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate ```
class manually.

The example below shows an example of how you could use the facade to get the available currencies:

```php
<?php
    
    namespace App\Http\Controllers;
    
    use ExchangeRate;
    
    class TestController extends Controller
    {
        public function index()
        {
            return ExchangeRate::currencies();
        }
    }
```
### Examples
This example shows how to convert 100 pence (£1) from Great British Pounds to Euros. The current exchange rate will be used (unless a cached rate for this date already exists).
```php
<?php
    
    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
    
    class TestController extends Controller
    {
        public function index()
        {
            $exchangeRates = new ExchangeRate();
            return $exchangeRates->convert(100, 'GBP', 'EUR', Carbon::now());
        }
    }
```

### Caching
By default, the responses all of the requests to the [exchangeratesapi.io](http://exchangeratesapi.io) API are cached.
This allows for significant performance improvements and reduced bandwidth from your server. 

However, if for any reason you require a fresh result from the API and not a cached result, the ``` ->shouldBustCache() ```
method can be used. The example below shows how to ignore the cached value (if one exists) and make a new API request.

```php
<?php
    
    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
    
    class TestController extends Controller
    {
        public function index()
        {
            $exchangeRates = new ExchangeRate();
            return $exchangeRates->shouldBustCache()->convert(100, 'GBP', 'EUR', Carbon::now());
        }
    }
```

Note: The caching works by storing exchange rates after fetching them from the API. As an example, if you were to fetch
the exchange rates for 'GBP' to 'EUR' for 20-11-2019 - 27-11-2019, the rates between these dates will be cached as a single
cache item. This cache item will only be retrieved if you attempt to fetch the same exchange rates on with the exact same
currencies and date range.

Therefore, if you were to try and get 'GBP' to 'EUR' for 20-11-2019 - 26-11-2019, a new API request would be made because
the date range is different.

### Supported Currencies
Laravel Exchange Rates supports working with the following currencies (sorted in A-Z order):

| Code | Currency Name         |
|------|-----------------------|
| AUD  | Australian dollar     |
| BGN  | Bulgarian lev         |
| BRL  | Brazilian real        |
| CAD  | Canadian              |
| CHF  | Swiss franc           |
| CNY  | Chinese yuan renminbi |
| CZK  | Czech koruna          |
| DKK  | Danish krone          |
| EUR  | Euro                  |
| GBP  | Pound sterling        |
| HKD  | Hong Kong dollar      |
| HRK  | Croatian kuna         |
| HUF  | Hungarian forint      |
| IDR  | Indonesian rupiah     |
| ILS  | Israeli shekel        |
| INR  | Indian rupee          |
| ISK  | Icelandic krone       |
| JPY  | Japanese yen          |
| KRW  | South Korean won      |
| MXN  | Mexican peso          |
| MYR  | Malaysian ringgit     |
| NOK  | Norwegian krone       |
| NZD  | New Zealand dollar    |
| PHP  | Philippine peso       |
| PLN  | Polish zloty          |
| RON  | Romanian leu          |
| RUB  | Russian rouble        |
| SEK  | Swedish krona         |
| SGD  | Singapore dollar      |
| THB  | Thai baht             |
| TRY  | Turkish lira          |
| USD  | US dollar             |
| ZAR  | South African rand    |

Note: Please note that the currencies are available because they are exposed in the [exchangeratesapi.io](http://exchangeratesapi.io) API. 

## Testing

```bash
vendor/bin/phpunit
```

## Security

If you find any security related issues, please contact me directly at [mail@ashallendesign.co.uk](mailto:mail@ashallendesign.co.uk) to report it.

## Contribution

If you wish to make any changes or improvements to the package, feel free to make a pull request.

Note: A contribution guide will be added soon.

## Credits

- [Ash Allen](https://ashallendesign.co.uk)
- [Jess Pickup](https://jesspickup.co.uk) (Logo)
- [All Contributors](https://github.com/ash-jc-allen/short-url/graphs/contributors)

## Changelog
Check the [CHANGELOG](CHANGELOG.md) to get more information about the latest changes.

## Upgrading
Check the [UPGRADE](UPGRADE.md) guide to get more information on how to update this library to newer versions.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
