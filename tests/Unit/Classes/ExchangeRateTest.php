<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Classes;

use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesDataApi\ExchangeRatesDataApiDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApILegacy\ExchangeRatesApiLegacyDriver;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;

final class ExchangeRateTest extends TestCase
{
    /** @test */
    public function correct_default_driver_is_returned(): void
    {
        config(['laravel-exchange-rates.driver' => 'exchangeratesapi-legacy']);

        $driver = app(ExchangeRate::class)->driver();

        $this->assertSame(ExchangeRatesApiLegacyDriver::class, $driver::class);
    }

    /**
     * @test
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

    public function validDriversProvider(): array
    {
        return [
            ['exchangeratesapi-legacy', ExchangeRatesApiLegacyDriver::class],
            ['exchange-rates-data-api', ExchangeRatesDataApiDriver::class],
        ];
    }
}
