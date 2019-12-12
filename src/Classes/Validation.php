<?php

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException;
use Carbon\Carbon;

class Validation
{
    /**
     * Validate that the currency is supported by the
     * Exchange Rates API.
     *
     * @param string $currencyCode
     *
     * @throws InvalidCurrencyException
     */
    public static function validateCurrencyCode(string $currencyCode)
    {
        $currencies = new Currency();

        if (! $currencies->isAllowableCurrency($currencyCode)) {
            throw new InvalidCurrencyException($currencyCode.' is not a valid country code.');
        }
    }

    /**
     * Validate that both of the dates are in the
     * past. After this, check that the 'from'
     * date is not after the 'to' date.
     *
     * @param Carbon $from
     * @param Carbon $to
     *
     * @throws InvalidDateException
     */
    public static function validateStartAndEndDates(Carbon $from, Carbon $to)
    {
        self::validateDate($from);
        self::validateDate($to);

        if ($from->isAfter($to)) {
            throw new InvalidDateException('The \'from\' date must be before the \'to\' date.');
        }
    }

    /**
     * Validate the date that has been passed.
     * We check that the date is in the past.
     *
     * @param Carbon $date
     *
     * @throws InvalidDateException
     */
    public static function validateDate(Carbon $date)
    {
        if (! $date->isPast()) {
            throw new InvalidDateException('The date must be in the past.');
        }
    }
}
