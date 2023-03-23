# Upgrade Guide

## Upgrading from 6.* to 7.0.0

### Method Signature Change

As of v7.0.0, the `makeRequest` method in the `AshAllenDesign\LaravelExchangeRates\Interfaces\RequestSender` interface has been updated to return an `AshAllenDesign\LaravelExchangeRates\Interfaces\ResponseContract` interface instead of `mixed`.

If you are implementing this interface in your own code, you'll need to update the method signature to the new format. The new `makeRequest` method signature is as follows:

```php
public function makeRequest(string $path, array $queryParams = []): ResponseContract;
````

## Upgrading from 5.* to 6.0.0

### Updated the `ExchangeRate` Class to Use Drivers

As of Laravel Exchange Rates v6.0.0, the codebase is no longer tightly coupled to just using the `exchangeratesapi.io` API. Instead, it now uses the idea of drivers to allow you to easily switch between different APIs.

This means that the `src/Classes/ExchangeRate` class doesn't run any of the exchange rate or currency conversion logic itself anymore. Instead, it forwards your method calls to the driver classes you're using.

So from v6.0.0 onwards, this class should be resolved from the container rather than just using `new ExchangeRate()`. This is how you may have previously interacted with the package:

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRate = (new ExchangeRate())->exchangeRate(...);
```

Instead you should now use the `app` helper to resolve the class from the container like so:

```php
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

$exchangeRate = app(ExchangeRate::class)->exchangeRate(...);
```

If you've been using `http://api.exchangeratesapi.io/v1/` as your `api_url` config field, you shouldn't need to change anything in your config file or code.

However, if you've been using a different API, you won't be able to communicate with it anymore. You'll need to update your `driver` config field (we'll discuss this further down) to use of the supported APIs. If you wish to use an API service that doesn't have a supported driver, please feel free to make a PR to add it.

### Removed Config Field: `api_url`

The `api_url` config field has now been removed. This field was originally added as a temporary fix after the `exchangeratesapi.io` API started requiring an API key. It gave developers the ability to quickly switch to a different API that had the same API structure. But, now that we have drivers, this field is no longer needed.

You can safely remove this field from your `laravel-exchange-rates.php` config file.

### Added Config Field: `driver`

There's now a new `driver` config field that has been added.This field allows you to specify which API service you wish to use. By default, this field is set to `exchange-rates-api-io`. This means that you'll be using the `exchangeratesapi.io` API, as the default prior to v6.0.0.

At the time of releasing v6.0.0, the following drivers are supported:

- [Exchange Rates API](https://exchangeratesapi.io/)
- [Exchange Rates Data API](https://apilayer.com/marketplace/exchangerates_data-api)

If you wish to bring your `laravel-exchange-rates.php` config file in line with the latest changes and add this new field, you can add the following to your config file:

```php
/*
|--------------------------------------------------------------------------
| API Base URL
|--------------------------------------------------------------------------
|
| Define which API service should be used to retrieve the exchange rates.
|
| Supported: "exchange-rates-api-io", "exchange-rates-data-api"
|
*/
'driver' => 'exchange-rates-api-io',
````

### Removed `existsInCache` Method from `src/Classes/CacheRepository.php`

The `existsInCache` method has been removed from `src/Classes/CacheRepository.php` because it wasn't being used in the code base. 

If you are directly interacting with this class, this method won't be available anymore for you to use.

### Added type hints to methods in `src/Classes/CacheRepository.php`

The `buildCacheKey` method signature in the `src/Classes/CacheRepository.php` class has been updated to include a type hint for the `to` parameter.

Previously, the method signature was:

```php
public function buildCacheKey(string $from, $to, Carbon $date, Carbon $endDate = null): string
```

The method signature is now:

```php
public function buildCacheKey(string $from, string|array $to, Carbon $date, Carbon $endDate = null): string
```

If you are extending this class and overriding this method, you'll need to update your method signature to match the new one.


### Removed `validateIsStringOrArray` Method from `src/Classes/Validation.php`

This method was previously being used to assert that some methods parameters were either a string or an array. However, we're now using type hints to do this properly, so this method is no longer needed.

If you are directly interacting with this class, this method won't be available anymore for you to use.

### Added `array` type hint to `allowableCurrencies` property in `src/Classes/Currency.php`

The `allowableCurrencies` property in the `src/Classes/Currency.php` class has been updated to include a type hint for the `array` type.

If you are extending this class and overriding this property, you'll need to update your property to match the new one.

### Removed the `src/Classes/RequestBuilder` class

The `src/Classes/RequestBuilder` class has been removed because we're no longer only supporting a single API. Instead, each driver now has their own request builder that can be used to send correctly formed requests that the APIs accept.

For example, the `exchange-rates-api-io` driver has a `src/Drivers/ExchangeRatesApiIo/RequestBuilder.php` class that is used to build requests for the `exchangeratesapi.io` API. The `exchange-rates-data-api` driver has a `src/Drivers/ExchangeRatesDataApi/RequestBuilder.php` class that is used to build requests for the `https://apilayer.com/marketplace/exchangerates_data-api` API.

If you're using or extending the `src/Classes/RequestBuilder` class, you'll need to update your code to use the correct driver's request builder instead.

### Removed the "earliest date" validation

Previously, Laravel Exchange Rates only worked with the `exchangeratesapi.io` API. This API only allowed you to retrieve exchange rates for dates after the 4th January 1999. So, the package used to prevent any dates before this from being used and would throw an `AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException` exception if you tried to use a date before this.

As of v6.0.0, Laravel Exchange Rates now supports multiple APIs. Some of these APIs allow you to retrieve exchange rates for dates before the 4th January 1999. So, the package no longer prevents you from using dates before this. Instead, it will let the API handle the validation. If you'd still like to prevent dates before this from being used, you can add your own validation to your application's code.

## Upgrading from 4.* to 5.0.0

### Minimum PHP Version

As of v5.0.0, Laravel Exchange Rates no longer supports PHP 7.2, 7.3 or 7.4. So, you will require at least PHP 8.0.

### Dependency Upgrades

As of v5.0.0, Laravel Exchange Rates has dropped support for several package versions:

- Laravel 6 and 7 are no longer supported. You will need to use at least Laravel 8.
- `guzzlehttp/guzzle` 6.* is no longer supported as a dev dependency. You will need at least 7.0.
- `orchestra/testbench` 3.* , 4.* and 5.* are no longer supported as a dev dependency. You will need at least 5.0.
- `phpunit/phpunit` 8.* is no longer supported as a dev dependency. You will need at least 9.0.

## Upgrading from 3.* to 4.0.0

### Publish Config and Add Your API Key

As of 1st April 2021, the exchangeratesapi.io now requires an API key to use the service. To get an API key, head over to
[https://exchangeratesapi.io/pricing](https://exchangeratesapi.io/pricing). You can sign up for free or use the paid tiers.

Please note that at the time of writing this, you will need to be on at least the 'Basic' plan to make request via HTTPS. You
will also be required to have at least the 'Professional' plan to use the ` convertBetweenDateRange() ` and ` exchangeRateBetweenDateRange() `
that this package offers.

You will also be required to have at least the 'Basic' paid plan to use ` exchangeRate() ` and ` convert() ` methods offered by
this package due to the fact that the free plan does not allow setting a base currency when converting.

After you've got your API key, you can add the following fields to your ` .env ` file:

``` dotenv
EXCHANGE_RATES_API_URL=https://api.exchangeratesapi.io/v1/
EXCHANGE_RATES_API_KEY={Your-API-Key-Here}
```

<hr>

## Upgrading from 2.* to 3.0.0

### Minimum Required Laravel Version
As of Laravel Exchange Rates v3.0.0 the minimum required version of Laravel is no longer 5.8. You must be using at least
Laravel 6 to use this library.

### Method Signature Updates
The following methods have now been updated so that they can now support multiple exchange rates and conversions in one
method call:

- ``` exchangeRate() ```
- ``` exchangeRateBetweenDateRange() ```
- ``` convert() ```
- ``` convertBetweenDateRange() ```

The two methods ``` exchangeRate() ``` and ``` exchangeRateBetweenDateRange() ``` now accept either a string or array as the
second parameter. The two methods ``` convert() ``` and ``` convertBetweenDateRange()``` now accept either a string or array
as the third parameter.

If any of these methods are overridden in an inherited class, you will need to update your method signatures to match the following new signatures:

```
exchangeRate(string $from, $to, Carbon $date = null)
```
```
exchangeRateBetweenDateRange(string $from, $to, Carbon $date, Carbon $endDate, array $conversions = []): array
```
```
convert(int $value, string $from, $to, Carbon $date = null)
```
```
convertBetweenDateRange(int $value, string $from, $to, Carbon $date, Carbon $endDate, array $conversions = []): array
```

<hr>

## Upgrading from 1.* to 2.0.0

### Namespace Change
The namespace for the ``` ExchangeRate ``` class was originally ``` AshAllenDesign\LaravelExchangeRates ```. This has
now been updated to ``` AshAllenDesign\LaravelExchangeRates\Classes ``` to be consistent with other classes in the
library. Anywhere that this class has been used, you will need to update the import. 

The snippets below show an example of how the namespaces need updating:

Change from this:
```php
<?php

    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\ExchangeRate;
    ...

```

to this:
```php
<?php

    namespace App\Http\Controllers;
    
    use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
    ...

```
