<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\AllRatesToday;

use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\AllRatesTodayDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\Response;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Illuminate\Support\Facades\Cache;
use Mockery;

final class CurrenciesTest extends TestCase
{
    /** @test */
    public function currencies_come_from_the_keyless_table_without_an_api_key(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->allows('hasApiKey')->andReturnFalse();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/open/latest/USD'])
            ->once()
            ->andReturn(new Response(['rates' => ['USD' => 1, 'EUR' => 0.88, 'GBP' => 0.76]]));

        $currencies = (new AllRatesTodayDriver($requestBuilderMock))->currencies();

        $this->assertEquals(['USD', 'EUR', 'GBP'], $currencies);
        $this->assertNotNull(Cache::get('laravel_xr_currencies'));
    }

    /** @test */
    public function currencies_come_from_the_symbols_endpoint_with_an_api_key(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->allows('hasApiKey')->andReturnTrue();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/v1/symbols'])
            ->once()
            ->andReturn(new Response([
                'currencies' => [
                    ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
                    ['code' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦'],
                ],
                'count' => 2,
            ]));

        $currencies = (new AllRatesTodayDriver($requestBuilderMock))->currencies();

        $this->assertEquals(['USD', 'NGN'], $currencies);
    }

    /** @test */
    public function cached_currencies_are_returned_if_they_are_in_the_cache(): void
    {
        Cache::forever('laravel_xr_currencies', ['CUR1', 'CUR2', 'CUR3']);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')->never();

        $currencies = (new AllRatesTodayDriver($requestBuilderMock))->currencies();

        $this->assertEquals(['CUR1', 'CUR2', 'CUR3'], $currencies);
    }
}
