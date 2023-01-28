<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApILegacy\ExchangeRatesApiLegacyDriver;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ExchangeRateDriver;
use Illuminate\Support\Manager;

class ExchangeRate extends Manager
{
    public function createExchangeRatesApiLegacyDriver(): ExchangeRateDriver
    {
        return new ExchangeRatesApiLegacyDriver();
    }

    public function getDefaultDriver()
    {
        return config('laravel-exchange-rates.driver');
    }
}
