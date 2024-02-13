<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\ExchangeRatesDataApi;

use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesDataApi\ExchangeRatesDataApiDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesDataApi\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesDataApi\Response;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Illuminate\Support\Facades\Cache;
use Mockery;

final class CurrenciesTest extends TestCase
{
    /** @test */
    public function currencies_are_returned_as_an_array_if_no_currencies_are_cached(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/latest', []])
            ->once()
            ->andReturn($this->mockResponse());

        $exchangeRate = new ExchangeRatesDataApiDriver($requestBuilderMock);
        $currencies = $exchangeRate->currencies();

        $this->assertEquals($this->expectedResponse(), $currencies);

        $this->assertNotNull(Cache::get('laravel_xr_currencies'));
    }

    /** @test */
    public function cached_currencies_are_returned_if_they_are_in_the_cache(): void
    {
        Cache::forever('laravel_xr_currencies', ['CUR1', 'CUR2', 'CUR3']);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')->never();

        $exchangeRate = new ExchangeRatesDataApiDriver($requestBuilderMock);
        $currencies = $exchangeRate->currencies();

        $this->assertEquals(['CUR1', 'CUR2', 'CUR3'], $currencies);
    }

    /** @test */
    public function currencies_are_fetched_if_the_currencies_are_cached_but_the_should_bust_cache_method_called(): void
    {
        Cache::forever('currencies', ['CUR1', 'CUR2', 'CUR3']);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/latest', []])
            ->once()
            ->andReturn($this->mockResponse());

        $exchangeRate = new ExchangeRatesDataApiDriver($requestBuilderMock);
        $currencies = $exchangeRate->shouldBustCache()->currencies();

        $this->assertEquals($this->expectedResponse(), $currencies);
    }

    /** @test */
    public function currencies_are_not_cached_if_the_shouldCache_option_is_false(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/latest', []])
            ->once()
            ->andReturn($this->mockResponse());

        $exchangeRate = new ExchangeRatesDataApiDriver($requestBuilderMock);
        $currencies = $exchangeRate->shouldCache(false)->currencies();

        $this->assertEquals($this->expectedResponse(), $currencies);

        $this->assertNull(Cache::get('laravel_xr_currencies'));
    }

    private function mockResponse(): Response
    {
        return new Response([
            'rates' => [
                'CAD' => 1.4682,
                'HKD' => 8.7298,
                'ISK' => 138.1,
                'PHP' => 56.286,
                'DKK' => 7.4712,
                'HUF' => 328.33,
                'CZK' => 25.514,
                'AUD' => 1.6151,
                'RON' => 4.7547,
                'SEK' => 10.6993,
                'IDR' => 15640.93,
                'INR' => 78.816,
                'BRL' => 4.4437,
                'RUB' => 71.0786,
                'HRK' => 7.46,
                'JPY' => 120.43,
                'THB' => 33.623,
                'CHF' => 1.1013,
                'SGD' => 1.5129,
                'PLN' => 4.2535,
                'BGN' => 1.9558,
                'TRY' => 6.3761,
                'CNY' => 7.844,
                'NOK' => 10.1638,
                'NZD' => 1.7326,
                'ZAR' => 16.828,
                'USD' => 1.1139,
                'MXN' => 21.3164,
                'ILS' => 3.9272,
                'GBP' => 0.86008,
                'KRW' => 1300.09,
                'MYR' => 4.64,
            ],
            'base' => 'EUR',
            'date' => '2019-11-01',
        ]);
    }

    private function expectedResponse(): array
    {
        return [
            'EUR',
            'CAD',
            'HKD',
            'ISK',
            'PHP',
            'DKK',
            'HUF',
            'CZK',
            'AUD',
            'RON',
            'SEK',
            'IDR',
            'INR',
            'BRL',
            'RUB',
            'HRK',
            'JPY',
            'THB',
            'CHF',
            'SGD',
            'PLN',
            'BGN',
            'TRY',
            'CNY',
            'NOK',
            'NZD',
            'ZAR',
            'USD',
            'MXN',
            'ILS',
            'GBP',
            'KRW',
            'MYR',
        ];
    }
}
