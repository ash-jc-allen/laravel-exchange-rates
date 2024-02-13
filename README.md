<p align="center">
<img src="https://ashallendesign.co.uk/images/custom/laravel-exchange-rates-logo.png" alt="Laravel Exchange Rates" width="600">
</p>

<p align="center">
<a href="https://packagist.org/packages/ashallendesign/laravel-exchange-rates"><img src="https://img.shields.io/packagist/v/ashallendesign/laravel-exchange-rates.svg?style=flat-square" alt="Latest Version on Packagist"></a>
<a href="https://packagist.org/packages/ashallendesign/laravel-exchange-rates"><img src="https://img.shields.io/packagist/dt/ashallendesign/laravel-exchange-rates.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ashallendesign/laravel-exchange-rates"><img src="https://img.shields.io/packagist/php-v/ashallendesign/laravel-exchange-rates?style=flat-square" alt="PHP from Packagist"></a>
<a href="https://github.com/ash-jc-allen/short-url/blob/master/LICENSE"><img src="https://img.shields.io/github/license/ash-jc-allen/laravel-exchange-rates?style=flat-square" alt="GitHub license"></a>
</p>

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
- [Supported APIs](#supported-apis)
- [Configuration](#configuration)
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
    - [Drivers](#drivers)
      - [Available Drivers](#available-drivers)
    - [Validation Rule](#validation-rule)
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

A simple Laravel package used for interacting with exchange rates APIs. Laravel Exchange Rates allows you to get the latest or historical exchange rates and convert monetary values between different currencies.

## Installation

You can install the package via Composer:

```bash
composer require ashallendesign/laravel-exchange-rates
```

The package has been developed and tested to work with the following minimum requirements:

- PHP 8.0
- Laravel 8

## Supported APIs

Laravel Exchange Rates currently supports the following APIs:

- [Exchange Rates API](https://exchangeratesapi.io/)
- [Exchange Rates Data API](https://apilayer.com/marketplace/exchangerates_data-api)
- [Exchange Rate Host](https://exchangerate.host/)
- [CurrencyBeacon](https://currencybeacon.com/)

## Configuration

### Publish the Config File

You can publish the package's config file using the following command:
```bash
php artisan vendor:publish --provider="AshAllenDesign\LaravelExchangeRates\Providers\ExchangeRatesProvider"
```

If you're using an API that requires an API key, you can put it in your `.env`:

``` dotenv
EXCHANGE_RATES_API_KEY={Your-API-Key-Here}
```

If you're using the free tier of `https://exchangeratesapi.io` or `https://exchangerate.host`, you should set the `https` config option in `config/laravel-exchange-rates.php` to `false`, as the free tiers of those APIs don't allow HTTPS requests.

## Usage

### Methods

#### Available Currencies

To get the available currencies supported by the API, you can use the `currencies` method like so:

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$exchangeRates->currencies();
```

#### Exchange Rate

##### Getting the Rate Between Two Currencies

To get the exchange rate for one currency to another, you can use the `exchangeRate` method. When doing this, you can pass the currency code as a string as the second parameter. The `exchangeRate` method will then return a float containing the exchange rate.

The example below shows how to get the exchange rate from 'GBP' to 'EUR' for today.

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$result = $exchangeRates->exchangeRate('GBP', 'EUR');

// $result: 1.10086
```

Note: If a Carbon date is passed as the third parameter, the exchange rate for that day will be returned (if valid). If no date is passed, today's exchange rate will be used.

##### Getting the Rate Between More Than Two Currencies

It's possible to get the exchange rates for multiple currencies in one call. This can be particularly useful if you are needing to get many exchange rates at once and don't want to make multiple API calls.

To do this, you can use `exchangeRate` method and pass an array of currency code strings as the second parameter. This will return an array containing the exchange rates as floats.

The example below shows how to get the exchange rates from 'GBP' to 'EUR' and 'USD' for today:

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$result = $exchangeRates->exchangeRate('GBP', ['EUR', 'USD']);

// $result: [
//     'EUR' => 1.10086,
//     'USD' => 1.25622,
// ];
```

#### Exchange Rates Between Date Range

##### Getting the Rates Between Two Currencies

To get the exchange rates between two currencies between a given date range, you can use the `exchangeRateBetweenDateRange` method. When doing this, you can pass the currency code as a string as the second parameter. The method will then return an array containing the exchange rates.

The example below shows how to get the exchange rates from 'GBP' to 'EUR' for the past 3 days:

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$result = $exchangeRates->exchangeRateBetweenDateRange(
    'GBP',
    'EUR',
    Carbon::now()->subWeek(),
    Carbon::now()
);

// $result: [
//     '2020-07-07' => 1.1092623405
//     '2020-07-08' => 1.1120625424
//     '2020-07-09' => 1.1153867604
// ];
```

##### Getting the Rates Between More Than Two Currencies

To get the exchange rates for multiple currencies in one call, you can pass an array of currency codes strings as the second parameter to the `exchangeRateBetweenDateRange` method.

The example below shows how to get the exchange rates from 'GBP' to 'EUR' and 'USD' for the past 3 days.:

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$result = $exchangeRates->exchangeRateBetweenDateRange(
    'GBP',
    ['EUR', 'USD'],
    Carbon::now()->subDays(3),
    Carbon::now()
);

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

When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

##### Converting Between Two Currencies

Similar to how you can get the exchange rate from one currency to another, you can also convert a monetary value from one currency to another. To do this you can use the `convert()` method.

The example below shows how to convert £1 'GBP' to 'EUR' at today's exchange rate:

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$result = $exchangeRates->convert(100, 'GBP', 'EUR', Carbon::now());

// $result: 110.15884906
```

Note: If a Carbon date is passed as the third parameter, the exchange rate for that day will be returned (if valid). If no date is passed, today's exchange rate will be used.

##### Converting Between More Than Two Currencies

You can also use the `convert()` method to convert a monetary value from one currency to multiple currencies. To do this, you can pass an array of currency code strings as the third parameter.

The example below show how to convert £1 'GBP' to 'EUR' and 'USD' at today's exchange rate.

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$result = $exchangeRates->convert(
    100,
    'GBP',
    ['EUR', 'USD'],
    Carbon::now()
);

// $result: [
//     'EUR' => 110.15884906,
//     'USD' => 125.30569081
// ];
```

#### Convert Currencies Between Date Range

When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

##### Converting Between Two Currencies in a Date Range

Similar to getting the exchange rates between a date range, you can also get convert monetary values from one currency to another using the exchange rates. To do this you can use the `convertBetweenDateRange` method.

The example below shows how to convert £1 'GBP' to 'EUR' using the exchange rates for the past 3 days:

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$exchangeRates->convertBetweenDateRange(
    100,
    'GBP',
    'EUR',
    Carbon::now()->subDays(3),
    Carbon::now()
);

// $result: [
//     '2020-07-07' => 110.92623405,
//     '2020-07-08' => 111.20625424,
//     '2020-07-09' => 111.53867604,
// ];
```

##### Converting Between More Than Two Currencies in a Date Range

You can also use the `convertBetweenDateRange` method to convert a monetary value from one currency to multiple currencies
using the exchange rates between a date range. To do this, you can pass an array of currency codes strings as the third parameter.

The example below show how to convert £1 'GBP' to 'EUR' and 'USD' at the past three days' exchange rates.

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$result = $exchangeRates->exchangeRateBetweenDateRange(
    'GBP',
    ['EUR', 'USD'],
    Carbon::now()->subDays(3),
    Carbon::now()
);

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

If you prefer to use facades in Laravel, you can choose to use the provided `AshAllenDesign\LaravelExchangeRates\Facades\ExchangeRate` facade instead of instantiating the `AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate` class manually.

The example below shows an example of how you could use the facade to get the available currencies:

```php
use AshAllenDesign\LaravelExchangeRates\Facades\ExchangeRate;

ExchangeRate::currencies();
```

### Drivers

Laravel Exchange Rates provides multiple drivers so that you can interact with different exchange rate APIs.

You can select the default driver that you want to use by setting the `driver` config field in the `config/laravel-exchange-rates.php` config file. For example, to use the `exchange-rates-api-io` driver, you could set the following:

``` php
'driver' => 'exchange-rates-api-io',
```

This driver will automatically be used when running methods such as:

```php
// Using the "ExchangeRate" class:
$exchangeRates = app(ExchangeRate::class);
$result = $exchangeRates->exchangeRate('GBP', ['EUR', 'USD']);

// Using the "ExchangeRate" facade:
ExchangeRate::exchangeRate('GBP', ['EUR', 'USD']);
```

However, if you wish to use a different driver, you can use the `driver` method to specify the driver that you want to use. For example, to use the `exchange-rates-data-api` driver, you could use the following:

```php
// Using the "ExchangeRate" class:
$exchangeRates = app(ExchangeRate::class);

$result = $exchangeRates
    ->driver('exchange-rates-data-api')
    ->exchangeRate('GBP', ['EUR', 'USD']);

// Using the "ExchangeRate" facade:
ExchangeRate::driver('exchange-rates-data-api')
    ->exchangeRate('GBP', ['EUR', 'USD']);
```

#### Available Drivers

The following drivers are available with the package:

| API Service             | Driver name               | API URL                                                 |
|-------------------------|---------------------------|---------------------------------------------------------|
| CurrencyBeacon          | `currency-beacon`         | https://currencybeacon.com/                             |
| Exchange Rates API IO   | `exchange-rates-api-io`   | https://exchangeratesapi.io/                            |
| Exchange Rates Data API | `exchange-rates-data-api` | https://apilayer.com/marketplace/exchangerates_data-api |
| Exchange Rate Host      | `exchange-rate-host`      | https://exchangerate.host                               |

### Validation Rule

Laravel Exchange Rates comes with its own `ValidCurrency` rule for validating currencies. This can be useful for if you need to be sure that a currency (maybe one provided by the user) is supported by at least one driver. The example below show how you can use the rule for validating the currency.

```php
use AshAllenDesign\LaravelExchangeRates\Rules\ValidCurrency;
use Illuminate\Support\Facades\Validator;

$formData = [
    'currency' => 'GBP',
];

$rules = [
    'currency' => new ValidCurrency(),
];

$validator = Validator::make($formData, $rules);
```

This rule doesn't make a call to any APIs and instead checks an array (provided by the package) of all the currencies that are supported by at least one driver. This means that the rule will be much faster and won't cause issues with rate limits. However, it's possible that the rule may "pass" for a currency that is not supported by the driver that you are using.

If you'd prefer the validation to be stricter and return only the currencies supported by your chosen driver, you may want to write your own validation rule that makes a call to the API to get a list of supported currencies.

### Caching

#### Busting Cached Exchange Rates

By default, all responses from the exchange rates APIs are cached. This allows for significant performance improvements and reduced bandwidth from your server.

However, if for any reason you require a fresh result from the API and not a cached result, the `shouldBustCache` method can be used. The example below shows how to ignore the cached value (if one exists) and make a new API request:

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$exchangeRates->shouldBustCache()
    ->convert(
        100,
        'GBP',
        'EUR',
        Carbon::now()
);
```

#### Preventing Exchange Rates from Being Cached

It's also possible to prevent the exchange rates from being cached at all. To do this, you can use the `shouldCache(false)`
method. The example below shows how to get an exchange rate and not cache it:

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRates = app(ExchangeRate::class);

$exchangeRates->shouldCache(false)
    ->convert(
        100,
        'GBP',
        'EUR',
        Carbon::now()
    );
```

Note: The caching works by storing exchange rates after fetching them from the API. As an example, if you were to fetch
the exchange rates for 'GBP' to 'EUR' for 20-11-2019 - 27-11-2019, the rates between these dates will be cached as a single
cache item. This cache item will only be retrieved if you attempt to fetch the same exchange rates on with the exact same
currencies and date range.

Therefore, if you were to try and get 'GBP' to 'EUR' for 20-11-2019 - 26-11-2019, a new API request would be made because
the date range is different.

### Supported Currencies

Laravel Exchange Rates supports working with the following currencies (sorted in A-Z order):

| Code | Currency Name                       |
|------|-------------------------------------|
| AED  | United Arab Emirates Dirham         |
| AFN  | Afghan Afghani                      |
| ALL  | Albanian Lek                        |
| AMD  | Armenian Dram                       |
| ANG  | Netherlands Antillean Guilder       |
| AOA  | Angolan Kwanza                      |
| ARS  | Argentine Peso                      |
| AUD  | Australian Dollar                   |
| AWG  | Aruban Florin                       |
| AZN  | Azerbaijani Manat                   |
| BAM  | Bosnia-Herzegovina Convertible Mark |
| BBD  | Barbadian Dollar                    |
| BDT  | Bangladeshi Taka                    |
| BGN  | Bulgarian Lev                       |
| BHD  | Bahraini Dinar                      |
| BIF  | Burundian Franc                     |
| BMD  | Bermudan Dollar                     |
| BND  | Brunei Dollar                       |
| BOB  | Bolivian Boliviano                  |
| BRL  | Brazilian Real                      |
| BSD  | Bahamian Dollar                     |
| BTC  | Bitcoin                             |
| BTN  | Bhutanese Ngultrum                  |
| BWP  | Botswanan Pula                      |
| BYR  | Belarusian Ruble                    |
| BZD  | Belize Dollar                       |
| CAD  | Canadian Dollar                     |
| CDF  | Congolese Franc                     |
| CHF  | Swiss Franc                         |
| CLF  | Chilean Unit of Account (UF)        |
| CLP  | Chilean Peso                        |
| CNY  | Chinese Yuan                        |
| COP  | Colombian Peso                      |
| CRC  | Costa Rican Colón                   |
| CUC  | Cuban Convertible Peso              |
| CUP  | Cuban Peso                          |
| CVE  | Cape Verdean Escudo                 |
| CZK  | Czech Republic Koruna               |
| DJF  | Djiboutian Franc                    |
| DKK  | Danish Krone                        |
| DOP  | Dominican Peso                      |
| DZD  | Algerian Dinar                      |
| EGP  | Egyptian Pound                      |
| ERN  | Eritrean Nakfa                      |
| ETB  | Ethiopian Birr                      |
| EUR  | Euro                                |
| FJD  | Fijian Dollar                       |
| FKP  | Falkland Islands Pound              |
| GBP  | British Pound Sterling              |
| GEL  | Georgian Lari                       |
| GGP  | Guernsey Pound                      |
| GHS  | Ghanaian Cedi                       |
| GIP  | Gibraltar Pound                     |
| GMD  | Gambian Dalasi                      |
| GNF  | Guinean Franc                       |
| GTQ  | Guatemalan Quetzal                  |
| GYD  | Guyanaese Dollar                    |
| HKD  | Hong Kong Dollar                    |
| HNL  | Honduran Lempira                    |
| HRK  | Croatian Kuna                       |
| HTG  | Haitian Gourde                      |
| HUF  | Hungarian Forint                    |
| IDR  | Indonesian Rupiah                   |
| ILS  | Israeli New Sheqel                  |
| IMP  | Manx pound                          |
| INR  | Indian Rupee                        |
| IQD  | Iraqi Dinar                         |
| IRR  | Iranian Rial                        |
| ISK  | Icelandic Króna                     |
| JEP  | Jersey Pound                        |
| JMD  | Jamaican Dollar                     |
| JOD  | Jordanian Dinar                     |
| JPY  | Japanese Yen                        |
| KES  | Kenyan Shilling                     |
| KGS  | Kyrgystani Som                      |
| KHR  | Cambodian Riel                      |
| KMF  | Comorian Franc                      |
| KPW  | North Korean Won                    |
| KRW  | South Korean Won                    |
| KWD  | Kuwaiti Dinar                       |
| KYD  | Cayman Islands Dollar               |
| KZT  | Kazakhstani Tenge                   |
| LAK  | Laotian Kip                         |
| LBP  | Lebanese Pound                      |
| LKR  | Sri Lankan Rupee                    |
| LRD  | Liberian Dollar                     |
| LSL  | Lesotho Loti                        |
| LTL  | Lithuanian Litas                    |
| LVL  | Latvian Lats                        |
| LYD  | Libyan Dinar                        |
| MAD  | Moroccan Dirham                     |
| MDL  | Moldovan Leu                        |
| MGA  | Malagasy Ariary                     |
| MKD  | Macedonian Denar                    |
| MMK  | Myanma Kyat                         |
| MNT  | Mongolian Tugrik                    |
| MOP  | Macanese Pataca                     |
| MRO  | Mauritanian Ouguiya                 |
| MUR  | Mauritian Rupee                     |
| MVR  | Maldivian Rufiyaa                   |
| MWK  | Malawian Kwacha                     |
| MXN  | Mexican Peso                        |
| MYR  | Malaysian Ringgit                   |
| MZN  | Mozambican Metical                  |
| NAD  | Namibian Dollar                     |
| NGN  | Nigerian Naira                      |
| NIO  | Nicaraguan Córdoba                  |
| NOK  | Norwegian Krone                     |
| NPR  | Nepalese Rupee                      |
| NZD  | New Zealand Dollar                  |
| OMR  | Omani Rial                          |
| PAB  | Panamanian Balboa                   |
| PEN  | Peruvian Nuevo Sol                  |
| PGK  | Papua New Guinean Kina              |
| PHP  | Philippine Peso                     |
| PKR  | Pakistani Rupee                     |
| PLN  | Polish Zloty                        |
| PYG  | Paraguayan Guarani                  |
| QAR  | Qatari Rial                         |
| RON  | Romanian Leu                        |
| RSD  | Serbian Dinar                       |
| RUB  | Russian Ruble                       |
| RWF  | Rwandan Franc                       |
| SAR  | Saudi Riyal                         |
| SBD  | Solomon Islands Dollar              |
| SCR  | Seychellois Rupee                   |
| SDG  | Sudanese Pound                      |
| SEK  | Swedish Krona                       |
| SGD  | Singapore Dollar                    |
| SHP  | Saint Helena Pound                  |
| SLL  | Sierra Leonean Leone                |
| SOS  | Somali Shilling                     |
| SRD  | Surinamese Dollar                   |
| STD  | São Tomé and Príncipe Dobra         |
| SVC  | Salvadoran Colón                    |
| SYP  | Syrian Pound                        |
| SZL  | Swazi Lilangeni                     |
| THB  | Thai Baht                           |
| TJS  | Tajikistani Somoni                  |
| TMT  | Turkmenistani Manat                 |
| TND  | Tunisian Dinar                      |
| TOP  | Tongan Paʻanga                      |
| TRY  | Turkish Lira                        |
| TTD  | Trinidad and Tobago Dollar          |
| TWD  | New Taiwan Dollar                   |
| TZS  | Tanzanian Shilling                  |
| UAH  | Ukrainian Hryvnia                   |
| UGX  | Ugandan Shilling                    |
| USD  | United States Dollar                |
| UYU  | Uruguayan Peso                      |
| UZS  | Uzbekistan Som                      |
| VEF  | Venezuelan Bolívar Fuerte           |
| VND  | Vietnamese Dong                     |
| VUV  | Vanuatu Vatu                        |
| WST  | Samoan Tala                         |
| XAF  | CFA Franc BEAC                      |
| XAG  | Silver (troy ounce)                 |
| XAU  | Gold (troy ounce)                   |
| XCD  | East Caribbean Dollar               |
| XDR  | Special Drawing Rights              |
| XOF  | CFA Franc BCEAO                     |
| XPF  | CFP Franc                           |
| YER  | Yemeni Rial                         |
| ZAR  | South African Rand                  |
| ZMK  | Zambian Kwacha (pre-2013)           |
| ZMW  | Zambian Kwacha                      |
| ZWL  | Zimbabwean Dollar                   |

## Testing

If you are contributing new changes to the package, you can run the tests by running the following command from the packages's root folder:

```bash
composer test
```

## Security

If you find any security related issues, please contact me directly at [mail@ashallendesign.co.uk](mailto:mail@ashallendesign.co.uk) to report it.

## Contribution

If you wish to make any changes or improvements to the package, feel free to make a pull request.

To contribute to this library, please use the following guidelines before submitting your pull request:

- Write tests for any new functions that are added. If you are updating existing code, make sure that the existing tests
pass and write more if needed.
- Follow [PSR-2](https://www.php-fig.org/psr/psr-2/) coding standards.
- Make all pull requests to the `master` branch.

## Credits

- [Ash Allen](https://ashallendesign.co.uk)
- [Zak](https://github.com/thugic)
- [Jess Pickup](https://jesspickup.co.uk) (Logo)
- [All Contributors](https://github.com/ash-jc-allen/short-url/graphs/contributors)

## Changelog

Check the [CHANGELOG](CHANGELOG.md) to get more information about the latest changes.

## Upgrading

Check the [UPGRADE](UPGRADE.md) guide to get more information on how to update this library to newer versions.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support Me

If you've found this package useful, please consider buying a copy of [Battle Ready Laravel](https://battle-ready-laravel.com) to support me and my work.

Every sale makes a huge difference to me and allows me to spend more time working on open-source projects and tutorials.

To say a huge thanks, you can use the code **BATTLE20** to get a 20% discount on the book.

[👉 Get Your Copy!](https://battle-ready-laravel.com)

[![Battle Ready Laravel](https://ashallendesign.co.uk/images/custom/sponsors/battle-ready-laravel-horizontal-banner.png)](https://battle-ready-laravel.com)
