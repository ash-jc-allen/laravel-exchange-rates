<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use AshAllenDesign\LaravelExchangeRates\Drivers\CurrencyBeacon\CurrencyBeaconDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRateHost\ExchangeRateHostDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\ExchangeRatesApiIoDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesDataApi\ExchangeRatesDataApiDriver;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ExchangeRateDriver;
use Illuminate\Support\Manager;

class ExchangeRate extends Manager
{
    public function createCurrencyBeaconDriver(): ExchangeRateDriver
    {
        return new CurrencyBeaconDriver();
    }

    public function createExchangeRatesDataApiDriver(): ExchangeRateDriver
    {
        return new ExchangeRatesDataApiDriver();
    }

    public function createExchangeRatesApiIoDriver(): ExchangeRateDriver
    {
        return new ExchangeRatesApiIoDriver();
    }

    public function createExchangeRateHostDriver(): ExchangeRateDriver
    {
        return new ExchangeRateHostDriver();
    }

    public function getDefaultDriver()
    {
        return config('laravel-exchange-rates.driver');
    }
}
