<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesDataApi;

use AshAllenDesign\LaravelExchangeRates\Drivers\Support\Driver;

/**
 * @see https://apilayer.com/marketplace/exchangerates_data-api
 */
class ExchangeRatesDataApiDriver extends Driver
{
    protected array $driverRequestBuilder = [RequestBuilder::class];
}
