<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\ExchangeRatesApiIo;

use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\ExchangeRatesApiIoDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\Response;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Mockery;

final class ExchangeRateBetweenDateRangeTest extends TestCase
{
    /** @test */
    public function exchange_rates_between_date_range_are_returned_if_exchange_rates_are_not_cached(): void
    {
        $fromDate = now()->subWeek();
        $toDate = now();

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs([
                '/timeseries',
                [
                    'base' => 'GBP',
                    'start_date' => $fromDate->format('Y-m-d'),
                    'end_date' => $toDate->format('Y-m-d'),
                    'symbols' => 'EUR',
                ],
            ])
            ->once()
            ->andReturn($this->mockResponseForOneSymbol());

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
        $currencies = $exchangeRate->exchangeRateBetweenDateRange('GBP', 'EUR', $fromDate, $toDate);

        $expectedArray = [
            '2019-11-08' => 1.1606583254,
            '2019-11-06' => 1.1623446817,
            '2019-11-07' => 1.1568450522,
            '2019-11-05' => 1.1612648497,
            '2019-11-04' => 1.1578362356,
        ];

        $this->assertEquals($expectedArray, $currencies);
        $this->assertEquals($expectedArray,
            Cache::get('laravel_xr_GBP_EUR_'.$fromDate->format('Y-m-d').'_'.$toDate->format('Y-m-d')));
    }

    /** @test */
    public function cached_exchange_rates_are_returned_if_they_exist(): void
    {
        $fromDate = now()->subWeek();
        $toDate = now();

        $cacheKey = 'laravel_xr_GBP_EUR_'.$fromDate->format('Y-m-d').'_'.$toDate->format('Y-m-d');
        $cachedValues = $expectedArray = [
            '2019-11-08' => 1,
            '2019-11-06' => 2,
            '2019-11-07' => 3,
            '2019-11-05' => 4,
            '2019-11-04' => 5,
        ];
        Cache::forever($cacheKey, $cachedValues);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')->never();

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
        $currencies = $exchangeRate->exchangeRateBetweenDateRange('GBP', 'EUR', $fromDate, $toDate);

        $this->assertEquals($expectedArray, $currencies);
        $this->assertEquals($expectedArray,
            Cache::get('laravel_xr_GBP_EUR_'.$fromDate->format('Y-m-d').'_'.$toDate->format('Y-m-d')));
    }

    /** @test */
    public function cached_exchange_rates_are_ignored_if_should_bust_cache_method_is_called(): void
    {
        $fromDate = now()->subWeek();
        $toDate = now();

        $cacheKey = 'GBP_EUR_'.$fromDate->format('Y-m-d').'_'.$toDate->format('Y-m-d');
        $cachedValues = [
            '2019-11-08' => 1,
            '2019-11-06' => 2,
            '2019-11-07' => 3,
            '2019-11-05' => 4,
            '2019-11-04' => 5,
        ];
        Cache::forever($cacheKey, $cachedValues);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs([
                '/timeseries',
                [
                    'base' => 'GBP',
                    'start_date' => $fromDate->format('Y-m-d'),
                    'end_date' => $toDate->format('Y-m-d'),
                    'symbols' => 'EUR',
                ],
            ])
            ->once()
            ->andReturn($this->mockResponseForOneSymbol());

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
        $currencies = $exchangeRate->shouldBustCache()->exchangeRateBetweenDateRange('GBP', 'EUR', $fromDate, $toDate);

        $expectedArray = [
            '2019-11-08' => 1.1606583254,
            '2019-11-06' => 1.1623446817,
            '2019-11-07' => 1.1568450522,
            '2019-11-05' => 1.1612648497,
            '2019-11-04' => 1.1578362356,
        ];

        $this->assertEquals($expectedArray, $currencies);
        $this->assertEquals($expectedArray,
            Cache::get('laravel_xr_GBP_EUR_'.$fromDate->format('Y-m-d').'_'.$toDate->format('Y-m-d')));
    }

    /** @test */
    public function exchange_rates_are_not_cached_if_the_shouldCache_option_is_false(): void
    {
        $fromDate = now()->subWeek();
        $toDate = now();

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs([
                '/timeseries',
                [
                    'base' => 'GBP',
                    'start_date' => $fromDate->format('Y-m-d'),
                    'end_date' => $toDate->format('Y-m-d'),
                    'symbols' => 'EUR',
                ],
            ])
            ->once()
            ->andReturn($this->mockResponseForOneSymbol());

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
        $currencies = $exchangeRate->shouldCache(false)->exchangeRateBetweenDateRange('GBP', 'EUR', $fromDate, $toDate);

        $expectedArray = [
            '2019-11-08' => 1.1606583254,
            '2019-11-06' => 1.1623446817,
            '2019-11-07' => 1.1568450522,
            '2019-11-05' => 1.1612648497,
            '2019-11-04' => 1.1578362356,
        ];

        $this->assertEquals($expectedArray, $currencies);
        $this->assertNull(Cache::get('laravel_xr_GBP_EUR_'.$fromDate->format('Y-m-d').'_'.$toDate->format('Y-m-d')));
    }

    /** @test */
    public function multiple_exchange_rates_between_date_range_are_returned_if_exchange_rates_are_not_cached(): void
    {
        $fromDate = now()->subWeek();
        $toDate = now();

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs([
                '/timeseries',
                [
                    'base' => 'GBP',
                    'start_date' => $fromDate->format('Y-m-d'),
                    'end_date' => $toDate->format('Y-m-d'),
                    'symbols' => 'EUR,USD',
                ],
            ])
            ->once()
            ->andReturn($this->mockResponseForMultipleSymbols());

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
        $currencies = $exchangeRate->exchangeRateBetweenDateRange('GBP', ['EUR', 'USD'], $fromDate, $toDate);

        $expectedArray = [
            '2019-11-08' => ['EUR' => 1.1606583254, 'USD' => 1.1111111111],
            '2019-11-06' => ['EUR' => 1.1623446817, 'USD' => 1.2222222222],
            '2019-11-07' => ['EUR' => 1.1568450522, 'USD' => 1.3333333333],
            '2019-11-05' => ['EUR' => 1.1612648497, 'USD' => 1.4444444444],
            '2019-11-04' => ['EUR' => 1.1578362356, 'USD' => 1.5555555555],
        ];

        $this->assertEquals($expectedArray, $currencies);
        $this->assertEquals($expectedArray,
            Cache::get('laravel_xr_GBP_EUR_USD_'.$fromDate->format('Y-m-d').'_'.$toDate->format('Y-m-d'))
        );
    }

    /** @test */
    public function request_is_not_made_if_the_currencies_are_the_same(): void
    {
        $fromDate = Carbon::createFromDate(2019, 11, 4);
        $toDate = Carbon::createFromDate(2019, 11, 10);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')->withAnyArgs()->never();

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
        $currencies = $exchangeRate->exchangeRateBetweenDateRange('EUR', 'EUR', $fromDate, $toDate);

        $expectedArray = [
            '2019-11-08' => 1.0,
            '2019-11-06' => 1.0,
            '2019-11-07' => 1.0,
            '2019-11-05' => 1.0,
            '2019-11-04' => 1.0,
        ];

        $this->assertEquals($expectedArray, $currencies);

        $this->assertEquals($expectedArray,
            Cache::get('laravel_xr_EUR_EUR_'.$fromDate->format('Y-m-d').'_'.$toDate->format('Y-m-d')));
    }

    /** @test */
    public function exception_is_thrown_if_the_date_parameter_passed_is_in_the_future(): void
    {
        $this->expectException(InvalidDateException::class);
        $this->expectExceptionMessage('The date must be in the past.');

        $exchangeRate = new ExchangeRatesApiIoDriver();
        $exchangeRate->exchangeRateBetweenDateRange('EUR', 'GBP', now()->addMinute(), now()->subDay());
    }

    /** @test */
    public function exception_is_thrown_if_the_end_date_parameter_passed_is_in_the_future(): void
    {
        $this->expectException(InvalidDateException::class);
        $this->expectExceptionMessage('The date must be in the past.');

        $exchangeRate = new ExchangeRatesApiIoDriver();
        $exchangeRate->exchangeRateBetweenDateRange('EUR', 'GBP', now()->subDay(), now()->addMinute());
    }

    /** @test */
    public function exception_is_thrown_if_the_end_date_is_before_the_start_date(): void
    {
        $this->expectException(InvalidDateException::class);
        $this->expectExceptionMessage("The 'from' date must be before the 'to' date.");

        $exchangeRate = new ExchangeRatesApiIoDriver();
        $exchangeRate->exchangeRateBetweenDateRange('EUR', 'GBP', now()->subDay(), now()->subWeek());
    }

    /** @test */
    public function exception_is_thrown_if_the_from_parameter_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('INVALID is not a valid currency code.');

        $exchangeRate = new ExchangeRatesApiIoDriver();
        $exchangeRate->exchangeRateBetweenDateRange('INVALID', 'GBP', now()->subWeek(), now()->subDay());
    }

    /** @test */
    public function exception_is_thrown_if_the_to_parameter_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('INVALID is not a valid currency code.');

        $exchangeRate = new ExchangeRatesApiIoDriver();
        $exchangeRate->exchangeRateBetweenDateRange('GBP', 'INVALID', now()->subWeek(), now()->subDay());
    }

    /** @test */
    public function exception_is_thrown_if_one_of_the_to_parameter_currencies_are_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('INVALID is not a valid currency code.');

        $exchangeRate = new ExchangeRatesApiIoDriver();
        $exchangeRate->exchangeRateBetweenDateRange('GBP', ['USD', 'INVALID'], now()->subWeek(), now()->subDay());
    }

    private function mockResponseForOneSymbol(): Response
    {
        return new Response([
            'rates' => [
                '2019-11-08' => [
                    'EUR' => 1.1606583254,
                ],
                '2019-11-06' => [
                    'EUR' => 1.1623446817,
                ],
                '2019-11-07' => [
                    'EUR' => 1.1568450522,
                ],
                '2019-11-05' => [
                    'EUR' => 1.1612648497,
                ],
                '2019-11-04' => [
                    'EUR' => 1.1578362356,
                ],
            ],
            'start_date' => '2019-11-03',
            'base' => 'GBP',
            'end_date' => '2019-11-10',
        ]);
    }

    private function mockResponseForMultipleSymbols(): Response
    {
        return new Response([
            'rates' => [
                '2019-11-08' => [
                    'EUR' => 1.1606583254,
                    'USD' => 1.1111111111,
                ],
                '2019-11-06' => [
                    'EUR' => 1.1623446817,
                    'USD' => 1.2222222222,
                ],
                '2019-11-07' => [
                    'EUR' => 1.1568450522,
                    'USD' => 1.3333333333,
                ],
                '2019-11-05' => [
                    'EUR' => 1.1612648497,
                    'USD' => 1.4444444444,
                ],
                '2019-11-04' => [
                    'EUR' => 1.1578362356,
                    'USD' => 1.5555555555,
                ],
            ],
            'start_date' => '2019-11-03',
            'base' => 'GBP',
            'end_date' => '2019-11-10',
        ]);
    }
}
