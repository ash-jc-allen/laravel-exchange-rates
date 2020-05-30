# Changelog

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