<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\ExchangeRatesApiIoDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesDataApi\ExchangeRatesDataApiDriver;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ExchangeRateDriver;
use Illuminate\Support\Manager;

class ExchangeRate extends Manager
{
    public function createExchangeRatesDataApiDriver(): ExchangeRateDriver
    {
        return new ExchangeRatesDataApiDriver();
    }

    public function createExchangeRatesApiIoDriver(): ExchangeRateDriver
    {
        return new ExchangeRatesApiIoDriver();
    }

    public function getDefaultDriver()
    {
        return config('laravel-exchange-rates.driver');
    }
}
