<?php

namespace AshAllenDesign\LaravelExchangeRates\Providers;

use AshAllenDesign\LaravelExchangeRates\Facades\ExchangeRate;
use Illuminate\Support\ServiceProvider;

class ExchangeRatesProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias(ExchangeRate::class, 'exchange');
    }

    public function boot()
    {
        $this->publishes([dirname(__DIR__, 1).'/config/exchange-rates.php' => config_path('exchange-rates.php')]);
        $this->mergeConfigFrom(dirname(__DIR__, 1).'/config/exchange-rates.php', 'exchange-rates');
    }
}
