<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\CurrencyBeacon;

use AshAllenDesign\LaravelExchangeRates\Drivers\CurrencyBeacon\CurrencyBeaconDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\CurrencyBeacon\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Drivers\CurrencyBeacon\Response;
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
            ->withArgs(['/currencies', ['type' => 'fiat']])
            ->once()
            ->andReturn($this->mockResponse());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
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

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $currencies = $exchangeRate->currencies();

        $this->assertEquals(['CUR1', 'CUR2', 'CUR3'], $currencies);
    }

    /** @test */
    public function currencies_are_fetched_if_the_currencies_are_cached_but_the_should_bust_cache_method_called(): void
    {
        Cache::forever('currencies', ['CUR1', 'CUR2', 'CUR3']);

        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/currencies', ['type' => 'fiat']])
            ->once()
            ->andReturn($this->mockResponse());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $currencies = $exchangeRate->shouldBustCache()->currencies();

        $this->assertEquals($this->expectedResponse(), $currencies);
    }

    /** @test */
    public function currencies_are_not_cached_if_the_shouldCache_option_is_false(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class)->makePartial();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/currencies', ['type' => 'fiat']])
            ->once()
            ->andReturn($this->mockResponse());

        $exchangeRate = new CurrencyBeaconDriver($requestBuilderMock);
        $currencies = $exchangeRate->shouldCache(false)->currencies();

        $this->assertEquals($this->expectedResponse(), $currencies);

        $this->assertNull(Cache::get('laravel_xr_currencies'));
    }

    private function mockResponse(): Response
    {
        return new Response([
            'meta' => [
                'code' => 200,
                'disclaimer' => 'Usage subject to terms: https:\/\/currencybeacon.com\/terms',
            ],
            'response' => [
                [
                    'id' => 1,
                    'name' => 'UAE Dirham',
                    'short_code' => 'AED',
                    'code' => '784',
                    'precision' => 2,
                    'subunit' => 100,
                    'symbol' => 'د.إ',
                    'symbol_first' => true,
                    'decimal_mark' => '.',
                    'thousands_separator' => ',',
                ],
                [
                    'id' => 2,
                    'name' => 'Afghani',
                    'short_code' => 'AFN',
                    'code' => '971',
                    'precision' => 2,
                    'subunit' => 100,
                    'symbol' => '\u060b',
                    'symbol_first' => false,
                    'decimal_mark' => '.',
                    'thousands_separator' => ',',
                ],
                [
                    'id' => 3,
                    'name' => 'Lek',
                    'short_code' => 'ALL',
                    'code' => '8',
                    'precision' => 2,
                    'subunit' => 100,
                    'symbol' => 'L',
                    'symbol_first' => false,
                    'decimal_mark' => '.',
                    'thousands_separator' => ',',
                ],
                [
                    'id' => 4,
                    'name' => 'Armenian Dram',
                    'short_code' => 'AMD',
                    'code' => '51',
                    'precision' => 2,
                    'subunit' => 100,
                    'symbol' => '\u0564\u0580.',
                    'symbol_first' => false,
                    'decimal_mark' => '.',
                    'thousands_separator' => ',',
                ],
                [
                    'id' => 5,
                    'name' => 'Netherlands Antillean Guilder',
                    'short_code' => 'ANG',
                    'code' => '532',
                    'precision' => 2,
                    'subunit' => 100,
                    'symbol' => '\u0192',
                    'symbol_first' => true,
                    'decimal_mark' => ',',
                    'thousands_separator' => '.',
                ],

                // further currencies omitted for brevity
            ],
        ]);
    }

    private function expectedResponse(): array
    {
        return [
            'AED',
            'AFN',
            'ALL',
            'AMD',
            'ANG',
        ];
    }
}
