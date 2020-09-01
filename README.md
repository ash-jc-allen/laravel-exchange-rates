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
            - [Getting the Rate Between Two Currencies](#getting-the-rate-between-two-currencies)
            - [Getting the Rate Between More Than Two Currencies](#getting-the-rate-between-more-than-two-currencies)
        - [Exchange Rates Between Date Range](#exchange-rates-between-date-range)
            - [Getting the Rates Between Two Currencies](#getting-the-rates-between-two-currencies)
            - [Getting the Rates Between More Than Two Currencies](#getting-the-rates-between-more-than-two-currencies)
        - [Convert Currencies](#convert-currencies)
            - [Converting Between Two Currencies](#converting-between-two-currencies)
            - [Converting Between More Than Two Currencies](#converting-between-more-than-two-currencies)
        - [Convert Currencies Between Date Range](#convert-currencies-between-date-range)
            - [Converting Between Two Currencies in a Date Range](#converting-between-two-currencies-in-a-date-range)
            - [Converting Between More Than Two Currencies in a Date Range](#converting-between-more-than-two-currencies-in-a-date-range)
    - [Facade](#facade)
    - [Validation Rule](#validation-rule)
    - [Examples](#examples)
    - [Caching](#caching)
        - [Busting Cached Exchange Rates](#busting-cached-exchange-rates)
        - [Preventing Exchange Rates from Being Cached](#preventing-exchange-rates-from-being-cached)
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
- Laravel 6

## Usage

### Methods
#### Available Currencies
``` php
$exchangeRates = new ExchangeRate();
$exchangeRates->currencies();
```

#### Exchange Rate
##### Getting the Rate Between Two Currencies
To get the exchange for one currency to another, you can use the ``` ->exchangeRate() ``` method. When doing this, 
you can pass the currency code as a string as the second parameter. The ``` ->exchangeRates() ``` method will then
return a string containing the exchange rate.

The example below shows how to get the exchange rate from 'GBP' to 'EUR' for today.

```php
$exchangeRates = new ExchangeRate();
$result = $exchangeRates->exchangeRate('GBP', 'EUR');

// $result: '1.10086'
```

Note: If a Carbon date is passed as the third parameter, the exchange rate for that day will be returned (if valid).
If no date is passed, today's exchange rate will be used.

##### Getting the Rate Between More Than Two Currencies
It is possible to get the exchange rates for multiple currencies in one call. This can be particularly useful if you are needing
to get many exchange rates at once and do not want to multiple API calls.

To do this, you can use ``` ->exchangeRate() ``` method and pass an array of currency code strings as the second parameter.
This will return the an array containing the exchange rates as strings.

The example below shows how to get the exchange rates from 'GBP' to 'EUR' and 'USD' for today.

```php
$exchangeRates = new ExchangeRate();
$result = $exchangeRates->exchangeRate('GBP', ['EUR', 'USD']);

// $result: [
//     'EUR' => '1.10086',
//     'USD' => '1.25622'
// ];
```

#### Exchange Rates Between Date Range
##### Getting the Rates Between Two Currencies
To get the exchange rates between two currencies between a given date range, you can use the ``` ->exchangeRateBetweenDateRange() ```
method. When doing this, you can pass the currency code as a string as the second parameter. The method will then return
an array containing the exchange rates.

The example below shows how to get the exchange rates from 'GBP' to 'EUR' for the past 3 days. 

```php
$exchangeRates = new ExchangeRate();
$result = $exchangeRates->exchangeRateBetweenDateRange('GBP', 'EUR', Carbon::now()->subWeek(), Carbon::now());

// $result: [
//     '2020-07-07' => 1.1092623405
//     '2020-07-08' => 1.1120625424
//     '2020-07-09' => 1.1153867604
// ];
```

##### Getting the Rates Between More Than Two Currencies
To get the exchange rates for multiple currencies in one call, you can pass an array of currency codes strings as the second
parameter to the ``` ->exchangeRateBetweenDateRange() ``` method.

The example below shows how to get the exchange rates from 'GBP' to 'EUR' and 'USD' for the past 3 days. 

```php
$exchangeRates = new ExchangeRate();
$result = $exchangeRates->exchangeRateBetweenDateRange('GBP', ['EUR', 'USD'], Carbon::now()->subDays(3), Carbon::now());

// $result: [
//     '2020-07-07' => [
//         'EUR' => 1.1092623405,
//         'USD' => 1.2523571825,
//      ],
//     '2020-07-08' => [
//         'EUR' => 1.1120625424,
//         'USD' => 1.2550737853,
//      ],
//     '2020-07-09' => [
//         'EUR' => 1.1153867604,
//         'USD' => 1.2650716636,
//      ],
// ];
```

#### Convert Currencies
When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest
denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

##### Converting Between Two Currencies
Similar to how you can get the exchange rate from one currency to another, you can also convert a monetary value from one
currency to another. To do this you can use the ``` ->convert() ``` method.

The example below shows how to convert £1 'GBP' to 'EUR' at today's exchange rate.

```php
$exchangeRates = new ExchangeRate();
$result = $exchangeRates->convert(100, 'GBP', 'EUR', Carbon::now());

// $result: 110.15884906
```

Note: If a Carbon date is passed as the third parameter, the exchange rate for that day will be returned (if valid).
If no date is passed, today's exchange rate will be used.

##### Converting Between More Than Two Currencies
You can also use the ``` ->convert() ``` method to convert a monetary value from one currency to multiple currencies. To
do this, you can pass an array of currency codes strings as the third parameter.

The example below show how to convert £1 'GBP' to 'EUR' and 'USD' at today's exchange rate.

```php
$exchangeRates = new ExchangeRate();
$result = $exchangeRates->convert(100, 'GBP', ['EUR', 'USD'], Carbon::now());

// $result: [
//     'EUR' => 110.15884906,
//     'USD' => 125.30569081
// ];
```

#### Convert Currencies Between Date Range
When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest
denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

##### Converting Between Two Currencies in a Date Range
Similar to getting the exchange rates between a date range, you can also get convert monetary values from one currency to
another using the exchange rates. To do this you can use the ``` ->convertBetweenDateRange() ``` method.

The example below shows how to convert £1 'GBP' to 'EUR' using the exchange rates for the past 3 days.

```php
$exchangeRates = new ExchangeRate();
$exchangeRates->convertBetweenDateRange(100, 'GBP', 'EUR', Carbon::now()->subDays(3), Carbon::now());

// $result: [
//     '2020-07-07' => 110.92623405,
//     '2020-07-08' => 111.20625424,
//     '2020-07-09' => 111.53867604,
// ];
```

##### Converting Between More Than Two Currencies in a Date Range

You can also use the ``` ->convertBetweenDateRange() ``` method to convert a monetary value from one currency to multiple currencies
using the exchange rates between a date range. To do this, you can pass an array of currency codes strings as the third parameter.

The example below show how to convert £1 'GBP' to 'EUR' and 'USD' at the past three days' exchange rates.

```php
$exchangeRates = new ExchangeRate();
$result = $exchangeRates->exchangeRateBetweenDateRange('GBP', ['EUR', 'USD'], Carbon::now()->subDays(3), Carbon::now());

// $result: [
//     '2020-07-07' => [
//         'EUR' => 110.92623405,
//         'USD' => 125.23571825,
//      ],
//     '2020-07-08' => [
//         'EUR' => 111.20625424,
//         'USD' => 125.50737853,
//      ],
//     '2020-07-09' => [
//         'EUR' => 111.53867604,
//         'USD' => 126.50716636,
//      ],
// ];
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

### Validation Rule
Laravel Exchange Rates comes with its own ``` ValidCurrency ``` rule for validating currencies. This can be useful for if you need to be sure
that a currency (maybe one provided by the user) is supported by the library. The example below show how you can use the
rule for validating the currency.

```php
<?php
    
    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\Rules\ValidCurrency;
    use Illuminate\Support\Facades\Validator;

    class TestController extends Controller
    {
        public function index()
        {
            $formData = [
                'currency' => 'GBP',
            ];
    
            $rules = [
                'currency' => new ValidCurrency,
            ];
    
            $validator = Validator::make($formData, $rules);
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
#### Busting Cached Exchange Rates
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

#### Preventing Exchange Rates from Being Cached
It is also possible to prevent the exchange rates from being cached at all. To do this, you can use the ``` ->shouldCache(false) ```
method. The example below shows how to get an exchange rate and not cache it:

```php
<?php
    
    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
    
    class TestController extends Controller
    {
        public function index()
        {
            $exchangeRates = new ExchangeRate();
            return $exchangeRates->shouldCache(false)->convert(100, 'GBP', 'EUR', Carbon::now());
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

To contribute to this library, please use the following guidelines before submitting your pull request:

- Write tests for any new functions that are added. If you are updating existing code, make sure that the existing tests
pass and write more if needed.
- Follow [PSR-2](https://www.php-fig.org/psr/psr-2/) coding standards.
- Make all pull requests to the ``` master ``` branch.

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
