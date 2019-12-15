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
    public function register(): void
    {
        $this->app->alias(ExchangeRate::class, 'exchange');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {

    }
}
