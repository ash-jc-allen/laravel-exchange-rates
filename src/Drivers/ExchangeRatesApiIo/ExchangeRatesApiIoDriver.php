<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo;

use AshAllenDesign\LaravelExchangeRates\Drivers\Support\Driver;

/**
 * @see https://exchangeratesapi.io/
 */
class ExchangeRatesApiIoDriver extends Driver
{
    public array $driverRequestBuilder = [RequestBuilder::class];
}
