<?php

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit;

use AshAllenDesign\LaravelExchangeRates\Facades\ExchangeRate;
use AshAllenDesign\LaravelExchangeRates\Providers\ExchangeRatesProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider.
     *
     * @param $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ExchangeRatesProvider::class];
    }

    /**
     * Load package alias.
     *
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'exchange-rates' => ExchangeRate::class,
        ];
    }
}
