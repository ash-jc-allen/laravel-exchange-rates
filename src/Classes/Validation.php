<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use AshAllenDesign\LaravelExchangeRates\Exceptions\ExchangeRateException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException;
use Carbon\Carbon;

class Validation
{
    /**
     * Validate that the currency is supported by the exchange rates API.
     *
     * @param  string  $currencyCode
     *
     * @throws InvalidCurrencyException
     */
    public static function validateCurrencyCode(string $currencyCode): void
    {
        $currencies = new Currency();

        if (! $currencies->isAllowableCurrency($currencyCode)) {
            throw new InvalidCurrencyException($currencyCode.' is not a valid currency code.');
        }
    }

    /**
     * Validate that the currencies are all supported by the exchange rates API.
     *
     * @param  array  $currencyCodes
     *
     * @throws InvalidCurrencyException
     */
    public static function validateCurrencyCodes(array $currencyCodes): void
    {
        $currencies = new Currency();

        foreach ($currencyCodes as $currencyCode) {
            if (! $currencies->isAllowableCurrency($currencyCode)) {
                throw new InvalidCurrencyException($currencyCode.' is not a valid currency code.');
            }
        }
    }

    /**
     * Validate that both of the dates are in the past. After this, check that
     * the 'from' date is not after the 'to' date.
     *
     * @param  Carbon  $from
     * @param  Carbon  $to
     *
     * @throws InvalidDateException
     */
    public static function validateStartAndEndDates(Carbon $from, Carbon $to): void
    {
        self::validateDate($from);
        self::validateDate($to);

        if ($from->isAfter($to)) {
            throw new InvalidDateException('The \'from\' date must be before the \'to\' date.');
        }
    }

    /**
     * Validate the date that has been passed. We check that the date is in the past but
     * that it's not before the earliest possible date that the exchange rates support
     * (4th January 1999).
     *
     * @param  Carbon  $date
     *
     * @throws InvalidDateException
     */
    public static function validateDate(Carbon $date): void
    {
        if (! $date->isPast()) {
            throw new InvalidDateException('The date must be in the past.');
        }

        $earliestPossibleDate = Carbon::createFromDate(1999, 1, 4)->startOfDay();

        if ($date->isBefore($earliestPossibleDate)) {
            throw new InvalidDateException('The date cannot be before 4th January 1999.');
        }
    }

    /**
     * Validate that the parameter is a string or array.
     *
     * @param  mixed  $paramToValidate
     *
     * @throws ExchangeRateException
     */
    // TODO We can remove this if the type hinting is changed to string|array.
    public static function validateIsStringOrArray($paramToValidate): void
    {
        if (! is_string($paramToValidate) && ! is_array($paramToValidate)) {
            throw new ExchangeRateException($paramToValidate.' is not a string or array.');
        }
    }
}
