<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApILegacy\ExchangeRatesApiLegacyDriver;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ExchangeRateDriver;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;

class ExchangeRate extends Manager
{
    public function __construct(Container $container = null)
    {
        // TODO Is this safe to do?
        parent::__construct($container ?? app(Container::class));
    }

    public function createExchangeRatesApiLegacyDriver(): ExchangeRateDriver
    {
        return new ExchangeRatesApiLegacyDriver();
    }

    public function getDefaultDriver()
    {
        return config('laravel-exchange-rates.driver');
    }
}
