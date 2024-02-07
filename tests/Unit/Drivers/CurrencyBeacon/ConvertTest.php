<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\CurrencyBeacon;

use AshAllenDesign\LaravelExchangeRates\Drivers\CurrencyBeacon\CurrencyBeaconDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\CurrencyBeacon\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Drivers\CurrencyBeacon\Response;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Illuminate\Support\Facades\Cache;
use Mockery;

final class ConvertTest extends TestCase
{
    /** @test */
    public function converted_value_for_today_is_returned_if_no_date_parameter_passed_and_rate_is_not_cached(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/latest', ['base' => 'EUR', 'symbols' => 'GBP']])
            ->once()
            ->andReturn($this->mockResponseForCurrentDateAndOneSymbol());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->convert(100, 'EUR', 'GBP');
        $this->assertEquals('86.158', $rate);
        $this->assertEquals('0.86158', Cache::get('laravel_xr_EUR_GBP_'.now()->format('Y-m-d')));
    }

    /** @test */
    public function converted_value_in_the_past_is_returned_if_date_parameter_passed_and_rate_is_not_cached(): void
    {
        $mockDate = now();

        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs([
                '/historical',
                [
                    'base' => 'EUR',
                    'symbols' => 'GBP',
                    'date' => $mockDate->format('Y-m-d'),
                ],
            ])
            ->once()
            ->andReturn($this->mockResponseForPastDateAndOneSymbol());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->convert(100, 'EUR', 'GBP', $mockDate);
        $this->assertEquals('87.053', $rate);
        $this->assertEquals('0.87053', Cache::get('laravel_xr_EUR_GBP_'.$mockDate->format('Y-m-d')));
    }

    /** @test */
    public function cached_exchange_rate_is_used_if_it_exists(): void
    {
        $mockDate = now();

        Cache::forever('laravel_xr_EUR_GBP_'.$mockDate->format('Y-m-d'), 0.123456);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')->never();

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->convert(100, 'EUR', 'GBP', $mockDate);
        $this->assertEquals('12.3456', $rate);
        $this->assertEquals('0.123456', Cache::get('laravel_xr_EUR_GBP_'.$mockDate->format('Y-m-d')));
    }

    /** @test */
    public function cached_exchange_rate_is_not_used_if_should_bust_cache_method_is_called(): void
    {
        $mockDate = now();

        Cache::forever('laravel_xr_EUR_GBP_'.$mockDate->format('Y-m-d'), '0.123456');

        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs([
                '/historical',
                [
                    'base' => 'EUR',
                    'symbols' => 'GBP',
                    'date' => $mockDate->format('Y-m-d'),
                ],
            ])
            ->once()
            ->andReturn($this->mockResponseForPastDateAndOneSymbol());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->shouldBustCache()->convert(100, 'EUR', 'GBP', $mockDate);
        $this->assertEquals('87.053', $rate);
        $this->assertEquals('0.87053', Cache::get('laravel_xr_EUR_GBP_'.$mockDate->format('Y-m-d')));
    }

    /** @test */
    public function request_is_not_made_if_the_currencies_are_the_same(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')->withAnyArgs()->never();

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->convert(100, 'EUR', 'EUR');
        $this->assertEquals('100', $rate);
    }

    /** @test */
    public function converted_values_are_returned_for_today_with_multiple_currencies(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/latest', ['base' => 'EUR', 'symbols' => 'GBP,USD,CAD']])
            ->once()
            ->andReturn($this->mockResponseForCurrentDateAndMultipleSymbols());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->convert(100, 'EUR', ['GBP', 'USD', 'CAD']);

        $this->assertEqualsWithDelta(['CAD' => 145.61, 'USD' => 110.34, 'GBP' => 86.158], $rate, self::FLOAT_DELTA);

        $this->assertEquals(
            ['CAD' => 1.4561, 'USD' => 1.1034, 'GBP' => 0.86158],
            Cache::get('laravel_xr_EUR_CAD_GBP_USD_'.now()->format('Y-m-d'))
        );
    }

    /** @test */
    public function exception_is_thrown_if_the_date_parameter_passed_is_in_the_future(): void
    {
        $this->expectException(InvalidDateException::class);
        $this->expectExceptionMessage('The date must be in the past.');

        $exchangeRate = new CurrencyBeaconDriver();
        $exchangeRate->convert(100, 'EUR', 'GBP', now()->addMinute());
    }

    /** @test */
    public function exception_is_thrown_if_the_from_parameter_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('INVALID is not a valid currency code.');

        $exchangeRate = new CurrencyBeaconDriver();
        $exchangeRate->convert(100, 'INVALID', 'GBP', now()->subMinute());
    }

    /** @test */
    public function exception_is_thrown_if_the_to_parameter_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('INVALID is not a valid currency code.');

        $exchangeRate = new CurrencyBeaconDriver();
        $exchangeRate->convert(100, 'GBP', 'INVALID', now()->subMinute());
    }

    private function mockResponseForCurrentDateAndOneSymbol(): Response
    {
        return new Response([
            'meta' => [
                'code' => 200,
                'disclaimer' => 'Usage subject to terms: https:\/\/currencybeacon.com\/terms',
            ],
            'response' => [
                'date' => '2024-02-07T10:00:07Z',
                'base' => 'EUR',
                'rates' => [
                    'GBP' => 0.86158,
                ],
            ],
        ]);
    }

    private function mockResponseForPastDateAndOneSymbol(): Response
    {
        return new Response([
            'meta' => [
                'code' => 200,
                'disclaimer' => 'Usage subject to terms: https:\/\/currencybeacon.com\/terms',
            ],
            'response' => [
                'date' => '2023-10-27T10:00:07Z',
                'base' => 'EUR',
                'rates' => [
                    'GBP' => 0.87053,
                ],
            ],
        ]);
    }

    private function mockResponseForCurrentDateAndMultipleSymbols(): Response
    {
        return new Response([
            'meta' => [
                'code' => 200,
                'disclaimer' => 'Usage subject to terms: https:\/\/currencybeacon.com\/terms',
            ],
            'response' => [
                'date' => '2024-02-07T10:00:07Z',
                'base' => 'EUR',
                'rates' => [
                    'CAD' => 1.4561,
                    'USD' => 1.1034,
                    'GBP' => 0.86158,
                ],
            ],
        ]);
    }
}
