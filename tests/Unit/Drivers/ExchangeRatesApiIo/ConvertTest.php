<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\ExchangeRatesApiIo;

use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\ExchangeRatesApiIoDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\Response;
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

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
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
            ->withArgs(['/'.$mockDate->format('Y-m-d'), ['base' => 'EUR', 'symbols' => 'GBP']])
            ->once()
            ->andReturn($this->mockResponseForPastDateAndOneSymbol());

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
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

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
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
            ->withArgs(['/'.$mockDate->format('Y-m-d'), ['base' => 'EUR', 'symbols' => 'GBP']])
            ->once()
            ->andReturn($this->mockResponseForPastDateAndOneSymbol());

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
        $rate = $exchangeRate->shouldBustCache()->convert(100, 'EUR', 'GBP', $mockDate);
        $this->assertEquals('87.053', $rate);
        $this->assertEquals('0.87053', Cache::get('laravel_xr_EUR_GBP_'.$mockDate->format('Y-m-d')));
    }

    /** @test */
    public function request_is_not_made_if_the_currencies_are_the_same(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->expects('makeRequest')->withAnyArgs()->never();

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
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

        $exchangeRate = new ExchangeRatesApiIoDriver($requestBuilderMock);
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

        $exchangeRate = new ExchangeRatesApiIoDriver();
        $exchangeRate->convert(100, 'EUR', 'GBP', now()->addMinute());
    }

    /** @test */
    public function exception_is_thrown_if_the_from_parameter_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('INVALID is not a valid currency code.');

        $exchangeRate = new ExchangeRatesApiIoDriver();
        $exchangeRate->convert(100, 'INVALID', 'GBP', now()->subMinute());
    }

    /** @test */
    public function exception_is_thrown_if_the_to_parameter_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        $this->expectExceptionMessage('INVALID is not a valid currency code.');

        $exchangeRate = new ExchangeRatesApiIoDriver();
        $exchangeRate->convert(100, 'GBP', 'INVALID', now()->subMinute());
    }

    private function mockResponseForCurrentDateAndOneSymbol(): Response
    {
        return new Response([
            'rates' => [
                'CAD' => 1.4561,
                'HKD' => 8.6372,
                'ISK' => 137.7,
                'PHP' => 55.809,
                'DKK' => 7.4727,
                'HUF' => 333.37,
                'CZK' => 25.486,
                'AUD' => 1.6065,
                'RON' => 4.7638,
                'SEK' => 10.7025,
                'IDR' => 15463.05,
                'INR' => 78.652,
                'BRL' => 4.5583,
                'RUB' => 70.4653,
                'HRK' => 7.4345,
                'JPY' => 120.72,
                'THB' => 33.527,
                'CHF' => 1.0991,
                'SGD' => 1.5002,
                'PLN' => 4.261,
                'BGN' => 1.9558,
                'TRY' => 6.3513,
                'CNY' => 7.7115,
                'NOK' => 10.0893,
                'NZD' => 1.7426,
                'ZAR' => 16.3121,
                'USD' => 1.1034,
                'MXN' => 21.1383,
                'ILS' => 3.8533,
                'GBP' => 0.86158,
                'KRW' => 1276.66,
                'MYR' => 4.5609,
            ],
            'base' => 'EUR',
            'date' => '2019-11-08',
        ]);
    }

    private function mockResponseForPastDateAndOneSymbol(): Response
    {
        return new Response([
            'rates' => [
                'CAD' => 1.4969,
                'HKD' => 8.8843,
                'ISK' => 138.5,
                'PHP' => 60.256,
                'DKK' => 7.4594,
                'HUF' => 321.31,
                'CZK' => 25.936,
                'AUD' => 1.5663,
                'RON' => 4.657,
                'SEK' => 10.2648,
                'IDR' => 16661.6,
                'INR' => 82.264,
                'BRL' => 4.254,
                'RUB' => 76.4283,
                'HRK' => 7.43,
                'JPY' => 129.26,
                'THB' => 37.453,
                'CHF' => 1.1414,
                'SGD' => 1.5627,
                'PLN' => 4.288,
                'BGN' => 1.9558,
                'TRY' => 6.2261,
                'CNY' => 7.8852,
                'NOK' => 9.5418,
                'NZD' => 1.6815,
                'ZAR' => 16.1884,
                'USD' => 1.1346,
                'MXN' => 23.0001,
                'ILS' => 4.171,
                'GBP' => 0.87053,
                'KRW' => 1278.77,
                'MYR' => 4.7399,
            ],
            'base' => 'EUR',
            'date' => '2018-11-09',
        ]);
    }

    private function mockResponseForCurrentDateAndMultipleSymbols(): Response
    {
        return new Response([
            'rates' => [
                'CAD' => 1.4561,
                'USD' => 1.1034,
                'GBP' => 0.86158,
            ],
            'base' => 'EUR',
            'date' => '2019-11-08',
        ]);
    }
}
