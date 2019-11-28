<?php

namespace AshAllenDesign\LaravelExchangeRates\Classes;

class Currencies
{
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

    public function isAllowableCurrency(string $currency)
    {
        return in_array($currency, $this->allowableCurrencies);
    }
}
