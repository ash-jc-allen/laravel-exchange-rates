# Upgrade Guide

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