# Changelog

**v7.6.0 (released 2024-09-11):**

- Added "VES" as a valid currency symbol. [#161](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/161)

**v7.5.0 (released 2024-07-10):**

- Added "BYN" as a valid currency symbol. [#157](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/157)

**7.4.0 (released 2024-03-19):**

- Added support for `nesbot/carbon 3.0`. [#154](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/154)

**v7.3.0 (released 2024-03-13):**

- Added support for Laravel 11. [#153](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/153)

**v7.2.0 (released 2024-02-13):**

- Added support for the CurrencyBeacon API. [#149](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/149)

**v7.1.1 (released 2023-10-29):**

- Fixed the `exchange-rate-host` driver so it works with the latest, sudden API changes. [#148](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/148)  

**v7.1.0 (released 2023-10-28):**

- Added an `https` config option that can be used to specify whether the API should be accessed over HTTPS or HTTP. [#136](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/136), [#137](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/137)
- Run CI tests with PHP 8.3. [#134](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/134)

**v7.0.1 (released 2023-10-13):**

- Fixed bug that was using the wrong response class. [#130](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/130)
- Fixed bug that was caused by returning a string rather than a float. [#128](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/128)

**v7.0.0 (released 2023-03-23):**

- Updated documentation to show the correct way to use the package. [#123](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/123)
- Updated the `RequestSender` interface (and all classes implementing it) to return a `ResponseContract` interface from the `makeRequest` method instead of `mixed`. [#126](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/126)

**v6.1.0 (released 2023-02-10):**

- Added support for the "exchangerate.host" API. [#118](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/118)

**v6.0.0 (released 2023-02-08):**

- Added concept of "drivers" so the package can support multiple APIs. [#84](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/84)
- Added support for API Layer's "Exchange Rates Data API". [#89](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/89)
- Added a new `driver` config option. [#84](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/84)
- Added type hints and return types. [#106](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/106)
- Added `declare(strict_types=1);` to all files. [#106](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/106)
- Added specific Larastan configuration for Laravel 8. [#113](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/113)
- Added more tests. [#106](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/101)
- Refactored method signatures to remove unneeded arrays. [#113](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/113)
- Updated documentation. [#103](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/103)
- Run CI workflows for PHP 8.1 and Laravel 10. [#90](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/90)
- Removed the `api_url` config option. [#84](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/84)
- Removed `existsInCache` method from the `src/Classes/CacheRepository.php` file. [#106](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/106)
- Removed `validateIsStringOrArray` method from the `src/Classes/Validation` file. [#106](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/106)
- Removed the validation that ensured a date wasn't before the 4th January 1999. [#117](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/117)

**v5.2.0 (released 2023-01-11):**
- Added support for Laravel 10. [#81](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/81)
- Updated the PHPUnit config to use the newer format. [#80](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/80)

**v5.1.0 (released 2022-09-12):**
- Added support for PHP 8.2. [#78](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/78)

**v5.0.1 (released 2022-02-11):**
- Used `Carbon::now()` instead of the `now()` helper to provide support for Lumen. [#77](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/77)

**v5.0.0 (released 2022-01-26):**
- Added PHPStan workflow for GitHub Actions. [#72](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/72)
- Migrated tests workflow from Travis CI to GitHub Actions. [#69](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/69)
- Added support for Laravel 9.*. [#69](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/69)
- Dropped support for Laravel 6.* and 7.*. [#69](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/69)
- Dropped support for PHP 7.2, 7.3 and 7.4. [#69](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/69)
- Dropped support for `guzzlehttp/guzzle` 6.*. [#69](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/69)
- Dropped support for `orchestra/testbench` 3.* , 4.* , and 5.*. [#69](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/69)
- Dropped support for `phpunit/phpunit` 8.*. [#69](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/69)
- Added FUNDING.yml.

**v4.1.0 (released 2021-05-03):**
- Updated the list of allowed currencies. [#66](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/66)
- Updated Dependabot to GitHub-native version. [#64](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/64)

**v4.0.1 (released 2021-04-12):**
- Fixed the parameters that are sent to the ` timeseries ` endpoint. [#63](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/63)

**v4.0.0 (released 2021-04-01):**
- Added a config file and updated routes to work with the API updates. [#57](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/57)

**v3.3.0 (released 2020-12-06):**
- Added support for PHP 8. [#50](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/50)

**v3.2.1 (released 2020-09-16):**
- Updated the Travis CI config to run the tests on the correct Laravel versions. [#47](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/47)

**v3.2.0 (released 2020-09-08):**
- Added support for Laravel 8 and Guzzle 7. [#46](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/46)

**v3.1.0 (released 2020-09-01):**
- Added a new ``` ValidCurrency``` rule that can be used for validating currencies in requests.
[#45](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/45)

**v3.0.0 (released 2020-07-12):**
- Added the functionality to get the exchange rates and converted values for more than one currency at a time. [#42](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/42)
- Added a new ``` ->shouldCache() ``` method that can be used to determine if an exchange rate should be cached after fetching it from the API. [#38](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/38)
- Dropped support for Laravel 5.8 and made Laravel 6 the minimum supported version. [#41](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/41)
- Miscellaneous bug fixes. [#39](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/39)

**2.2.0 (released 2020-05-30):**
- Prevented requests from being made to the API if trying to get the exchange rate between the same currencies. [#32](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/32)
- Fixed bug that was caused if trying to get the exchange rate for 'EUR' to 'EUR'. [#32](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/32)
- Updated documentation. [#33](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/33)

**2.1.0 (released 2020-03-05):**
- Added support for Laravel 7. [#28](https://github.com/ash-jc-allen/laravel-exchange-rates/pull/28)

**2.0.0 (released 2020-01-04):**
- Added an ``` ExchangeRate ``` facade that can auto-discovered by Laravel.
- Updated documentation.
- Added the package logo.
- Added a changelog.
- Added an upgrade guide.
- Removed an unneeded license file.
- Updated unit tests and continuous integration configuration.

**1.0.2 (released 2020-01-04):**
- Fixed bug that prevented most web requests being made to the exchangesratesapi.io API.

**1.0.1 (released 2019-12-15):**
- Removed a config file that was left in from the initial and is no longer needed for the library.

**1.0.0 (released 2019-12-14):**
- Release for production.
- Renamed the ``` Currencies ``` class to ``` Currency ``` to adhere to PSR standards.

**0.0.1 (pre-release):**
- Initial work and pre-release testing.
