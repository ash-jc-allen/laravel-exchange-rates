<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit;

use AshAllenDesign\LaravelExchangeRates\Facades\ExchangeRate;
use AshAllenDesign\LaravelExchangeRates\Providers\ExchangeRatesProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Allowed numerical distance between two values to consider them equal.
     */
    protected const FLOAT_DELTA = 0.0000000000001;

    /**
     * Load package service provider.
     *
     * @param  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ExchangeRatesProvider::class];
    }

    /**
     * Load package alias.
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'exchange-rates' => ExchangeRate::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['laravel-exchange-rates.api_key' => 'test-api-key']);
    }
}
