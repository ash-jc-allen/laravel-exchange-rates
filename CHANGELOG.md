# Changelog

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