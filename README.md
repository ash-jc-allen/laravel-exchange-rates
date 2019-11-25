# Laravel Exchange Rates

<div style="text-align:center">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ashallendesign/laravel-exchange-rates.svg?style=flat-square)](https://packagist.org/packages/ashallendesign/laravel-exchange-rates)
[![Build Status](https://img.shields.io/travis/ash-jc-allen/laravel-exchange-rates/master.svg?style=flat-square)](https://travis-ci.org/ash-jc-allen/laravel-exchange-rates)
[![Total Downloads](https://img.shields.io/packagist/dt/ashallendesign/laravel-exchange-rates.svg?style=flat-square)](https://packagist.org/packages/ashallendesign/laravel-exchange-rates)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/ashallendesign/laravel-exchange-rates?style=flat-square)](https://img.shields.io/packagist/php-v/ashallendesign/laravel-exchange-rates)
[![GitHub license](https://img.shields.io/github/license/ash-jc-allen/laravel-exchange-rates?style=flat-square)](https://github.com/ash-jc-allen/laravel-exchange-rates/blob/master/LICENSE)

</div>

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

### Available Currencies
``` php
<?php
    
    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\ExchangeRate;
    
    class TestController extends Controller
    {
        public function index()
        {
            $exchangeRates = new ExchangeRate();
            return $exchangeRates->currencies();
        }
    }
```

### Exchange Rate
Note: If a Carbon date is passed as the third parameter, the exchange rate for that day will be returned (if valid).
If no date is passed, today's exchange rate will be used.

``` php
<?php
    
    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\ExchangeRate;
    
    class TestController extends Controller
    {
        public function index()
        {
            $exchangeRates = new ExchangeRate();
            return $exchangeRates->exchangeRate('GBP', 'EUR');
        }
    }
```

### Exchange Rates Between Date Range
``` php
<?php
    
    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\ExchangeRate;
    
    class TestController extends Controller
    {
        public function index()
        {
            $exchangeRates = new ExchangeRate();
            return $exchangeRates->exchangeRateBetweenDateRange('GBP', 'EUR', Carbon::now()->subWeek(), Carbon::now());
        }
    }
```

### Convert Currencies
When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest
denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

Note: If a Carbon date is passed as the third parameter, the exchange rate for that day will be returned (if valid).
If no date is passed, today's exchange rate will be used.

``` php
<?php
    
    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\ExchangeRate;
    
    class TestController extends Controller
    {
        public function index()
        {
            $exchangeRates = new ExchangeRate();
            return $exchangeRates->convert(100, 'GBP', 'EUR', Carbon::now());
        }
    }
```

### Convert Currencies Between Date Range
When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest
denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

``` php
<?php
    
    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\ExchangeRate;
    
    class TestController extends Controller
    {
        public function index()
        {
            $exchangeRates = new ExchangeRate();
            return $exchangeRates->convert(100, 'GBP', 'EUR', Carbon::now()->subWeek(), Carbon::now());
        }
    }
```

### Supported Countries
Laravel Exchange Rates supports working with the following currencies (sorted in A-Z order):

- ***AUD*** - Australian dollar
- ***BGN*** - Bulgarian lev
- ***BRL*** - Brazilian real
- ***CAD*** - Canadian
- ***CHF*** - Swiss franc
- ***CNY*** - Chinese yuan renminbi
- ***CZK*** - Czech koruna
- ***DKK*** - Danish krone
- ***EUR*** - Euro
- ***GBP*** - Pound sterling
- ***HKD*** - Hong Kong dollar
- ***HRK*** - Croatian kuna
- ***HUF*** - Hungarian forint
- ***IDR*** - Indonesian rupiah
- ***ILS*** - Israeli shekel
- ***INR*** - Indian rupee
- ***ISK*** - Icelandic krone
- ***JPY*** - Japanese yen
- ***KRW*** - South Korean won
- ***MXN*** - Mexican peso
- ***MYR*** - Malaysian ringgit
- ***NOK*** - Norwegian krone
- ***NZD*** - New Zealand dollar
- ***PHP*** - Philippine peso
- ***PLN*** - Polish zloty
- ***RON*** - Romanian leu
- ***RUB*** - Russian rouble
- ***SEK*** - Swedish krona
- ***SGD*** - Singapore dollar
- ***THB*** - Thai baht
- ***TRY*** - Turkish lira
- ***USD*** - US dollar
- ***ZAR*** - South African rand

Note: Please note that the currencies are available because they are exposed in the [exchangeratesapi.io](http://exchangeratesapi.io) API. 

## Testing

``` bash
vendor/bin/phpunit
```

## Security

If you find any security related, please contact me directly at [mail@ashallendesign.co.uk](mailto:mail@ashallendesign.co.uk) to report it.

## Contribution

If you wish to make any changes or improvements to the package, feel free to make a pull request.

Note: A contribution guide will be added soon.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
