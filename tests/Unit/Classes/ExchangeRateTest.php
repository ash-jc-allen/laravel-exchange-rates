<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Classes;

use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use AshAllenDesign\LaravelExchangeRates\Drivers\CurrencyBeacon\CurrencyBeaconDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRateHost\ExchangeRateHostDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\ExchangeRatesApiIoDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesDataApi\ExchangeRatesDataApiDriver;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;

final class ExchangeRateTest extends TestCase
{
    /** @test */
    public function correct_default_driver_is_returned(): void
    {
        config(['laravel-exchange-rates.driver' => 'exchange-rates-api-io']);

        $driver = app(ExchangeRate::class)->driver();

        $this->assertSame(ExchangeRatesApiIoDriver::class, $driver::class);
    }

    /**
     * @test
     *
     * @dataProvider validDriversProvider
     */
    public function correct_driver_is_returned(string $driverName, string $driverClass): void
    {
        $driver = app(ExchangeRate::class)->driver($driverName);

        $this->assertSame($driverClass, $driver::class);
    }

    /** @test */
    public function exception_is_thrown_if_the_driver_is_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Driver [INVALID] not supported.');

        app(ExchangeRate::class)->driver('INVALID');
    }

    public static function validDriversProvider(): array
    {
        return [
            ['exchange-rates-api-io', ExchangeRatesApiIoDriver::class],
            ['exchange-rates-data-api', ExchangeRatesDataApiDriver::class],
            ['exchange-rate-host', ExchangeRateHostDriver::class],
            ['currency-beacon', CurrencyBeaconDriver::class],
        ];
    }
}
