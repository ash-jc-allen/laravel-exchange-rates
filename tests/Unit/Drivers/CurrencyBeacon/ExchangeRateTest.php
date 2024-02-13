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

final class ExchangeRateTest extends TestCase
{
    /** @test */
    public function exchange_rate_for_today_is_returned_if_no_date_parameter_passed_and_rate_is_not_cached(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/latest', ['base' => 'EUR', 'symbols' => 'GBP']])
            ->once()
            ->andReturn($this->mockResponseForCurrentDateAndOneSymbol());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->exchangeRate('EUR', 'GBP');
        $this->assertEquals('0.86158', $rate);
        $this->assertEquals('0.86158', Cache::get('laravel_xr_EUR_GBP_'.now()->format('Y-m-d')));
    }

    /** @test */
    public function exchange_rate_in_the_past_is_returned_if_date_parameter_passed_and_rate_is_not_cached(): void
    {
        $mockDate = now();

        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/historical', [
                'base' => 'EUR',
                'symbols' => 'GBP',
                'date' => $mockDate->format('Y-m-d'),
            ]])
            ->once()
            ->andReturn($this->mockResponseForPastDateAndOneSymbol());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->exchangeRate('EUR', 'GBP', $mockDate);
        $this->assertEquals('0.87053', $rate);
        $this->assertEquals('0.87053', Cache::get('laravel_xr_EUR_GBP_'.$mockDate->format('Y-m-d')));
    }

    /** @test */
    public function cached_exchange_rate_is_returned_if_it_exists(): void
    {
        $mockDate = now();

        Cache::forever('laravel_xr_EUR_GBP_'.$mockDate->format('Y-m-d'), 0.123456);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')->never();

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->exchangeRate('EUR', 'GBP', $mockDate);
        $this->assertEquals('0.123456', $rate);
        $this->assertEquals('0.123456', Cache::get('laravel_xr_EUR_GBP_'.$mockDate->format('Y-m-d')));
    }

    /** @test */
    public function multiple_exchange_rates_can_be_returned_if_no_date_parameter_passed_and_rate_is_not_cached(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/latest', ['base' => 'EUR', 'symbols' => 'GBP,USD,CAD']])
            ->once()
            ->andReturn($this->mockResponseForCurrentDateAndMultipleSymbols());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $response = $exchangeRate->exchangeRate('EUR', ['GBP', 'USD', 'CAD']);
        $this->assertEquals(['CAD' => 1.4561, 'USD' => 1.1034, 'GBP' => 0.86158], $response);
        $this->assertEquals(
            ['CAD' => 1.4561, 'USD' => 1.1034, 'GBP' => 0.86158],
            Cache::get('laravel_xr_EUR_CAD_GBP_USD_'.now()->format('Y-m-d'))
        );
    }

    /** @test */
    public function multiple_exchange_rates_can_be_returned_if_date_parameter_passed_and_rate_is_not_cached(): void
    {
        $mockDate = now();

        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs([
                '/historical',
                [
                    'base' => 'EUR',
                    'symbols' => 'GBP,CAD,USD',
                    'date' => $mockDate->format('Y-m-d'),
                ],
            ])
            ->once()
            ->andReturn($this->mockResponseForPastDateAndMultipleSymbols());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $response = $exchangeRate->exchangeRate('EUR', ['GBP', 'CAD', 'USD'], $mockDate);
        $this->assertEquals(['CAD' => 1.4969, 'USD' => 1.1346, 'GBP' => 0.87053], $response);
        $this->assertEquals(
            ['CAD' => 1.4969, 'USD' => 1.1346, 'GBP' => 0.87053],
            Cache::get('laravel_xr_EUR_CAD_GBP_USD_'.$mockDate->format('Y-m-d'))
        );
    }

    /** @test */
    public function multiple_cached_exchange_rates_are_returned_if_they_exist(): void
    {
        $mockDate = now();

        Cache::forget('laravel_xr_EUR_CAD_GBP_USD_'.$mockDate->format('Y-m-d'));

        Cache::forever('laravel_xr_EUR_CAD_GBP_USD_'.$mockDate->format('Y-m-d'),
            ['CAD' => 1.4561, 'USD' => 1.1034, 'GBP' => 0.86158]
        );

        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')->never();

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->exchangeRate('EUR', ['GBP', 'USD', 'CAD'], $mockDate);
        $this->assertEquals(['CAD' => 1.4561, 'USD' => 1.1034, 'GBP' => 0.86158], $rate);
        $this->assertEquals(
            ['CAD' => 1.4561, 'USD' => 1.1034, 'GBP' => 0.86158],
            Cache::get('laravel_xr_EUR_CAD_GBP_USD_'.$mockDate->format('Y-m-d'))
        );
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
        $rate = $exchangeRate->shouldBustCache()->exchangeRate('EUR', 'GBP', $mockDate);
        $this->assertEquals('0.87053', $rate);
        $this->assertEquals('0.87053', Cache::get('laravel_xr_EUR_GBP_'.$mockDate->format('Y-m-d')));
    }

    /** @test */
    public function exchange_rate_is_not_cached_if_the_shouldCache_option_is_false(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/latest', ['base' => 'EUR', 'symbols' => 'GBP']])
            ->once()
            ->andReturn($this->mockResponseForCurrentDateAndOneSymbol());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->shouldCache(false)->exchangeRate('EUR', 'GBP');
        $this->assertEquals('0.86158', $rate);
        $this->assertNull(Cache::get('laravel_xr_EUR_GBP_'.now()->format('Y-m-d')));
    }

    /** @test */
    public function request_is_not_made_if_the_symbols_are_the_same(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')->withAnyArgs()->never();

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $rate = $exchangeRate->exchangeRate('EUR', 'EUR');
        $this->assertEquals(1.0, $rate);
    }

    /** @test */
    public function exception_is_thrown_if_the_date_parameter_passed_is_in_the_future(): void
    {
        $this->expectException(InvalidDateException::class);
        $this->expectExceptionMessage('The date must be in the past.');

        $exchangeRate = new CurrencyBeaconDriver();
        $exchangeRate->exchangeRate('EUR', 'GBP', now()->addMinute());
    }

    /** @test */
    public function exception_is_thrown_if_the_from_parameter_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('INVALID is not a valid currency code.');

        $exchangeRate = new CurrencyBeaconDriver();
        $exchangeRate->exchangeRate('INVALID', 'GBP', now()->subMinute());
    }

    /** @test */
    public function exception_is_thrown_if_the_to_parameter_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('INVALID is not a valid currency code.');

        $exchangeRate = new CurrencyBeaconDriver();
        $exchangeRate->exchangeRate('GBP', 'INVALID', now()->subMinute());
    }

    /** @test */
    public function exception_is_thrown_if_the_to_parameter_array_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('INVALID is not a valid currency code.');

        $exchangeRate = new CurrencyBeaconDriver();
        $exchangeRate->exchangeRate('GBP', ['INVALID'], now()->subMinute());
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

    private function mockResponseForPastDateAndMultipleSymbols(): Response
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
                    'CAD' => 1.4969,
                    'USD' => 1.1346,
                    'GBP' => 0.87053,
                ],
            ],
        ]);
    }
}
