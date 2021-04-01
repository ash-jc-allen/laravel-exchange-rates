<?php

namespace AshAllenDesign\LaravelExchangeRates\Providers;

use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use Illuminate\Support\ServiceProvider;

class ExchangeRatesProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/laravel-exchange-rates.php', 'laravel-exchange-rates');

        $this->app->alias(ExchangeRate::class, 'exchange-rate');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/laravel-exchange-rates.php' => config_path('laravel-exchange-rates.php'),
        ], 'laravel-exchange-rates-config');
    }
}
