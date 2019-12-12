<?php

namespace AshAllenDesign\LaravelExchangeRates\Classes;

class Currency
{
    /**
     * Currencies supported by the API.
     *
     * @var array
     */
    public $allowableCurrencies = [
        'EUR',
        'CAD',
        'HKD',
        'ISK',
        'PHP',
        'DKK',
        'HUF',
        'CZK',
        'AUD',
        'RON',
        'SEK',
        'IDR',
        'INR',
        'BRL',
        'RUB',
        'HRK',
        'JPY',
        'THB',
        'CHF',
        'SGD',
        'PLN',
        'BGN',
        'TRY',
        'CNY',
        'NOK',
        'NZD',
        'ZAR',
        'USD',
        'MXN',
        'ILS',
        'GBP',
        'KRW',
        'MYR',
    ];

    /**
     * Determine if a currency that is being used is
     * allowable and supported by the API.
     *
     * @param  string  $currency
     * @return bool
     */
    public function isAllowableCurrency(string $currency): bool
    {
        return in_array($currency, $this->allowableCurrencies);
    }
}
