<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\AllRatesToday;

use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\AllRatesTodayDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\Response;
use AshAllenDesign\LaravelExchangeRates\Exceptions\ExchangeRateException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidCurrencyException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;

final class ExchangeRateTest extends TestCase
{
    /** @test */
    public function latest_rate_uses_the_keyless_endpoint_without_an_api_key(): void
    {
        $requestBuilderMock = $this->requestBuilder(hasApiKey: false);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/open/latest/EUR'])
            ->once()
            ->andReturn($this->keylessResponse());

        $rate = (new AllRatesTodayDriver($requestBuilderMock))->exchangeRate('EUR', 'GBP');

        $this->assertSame(0.86158, $rate);
        $this->assertEquals(0.86158, Cache::get('laravel_xr_EUR_GBP_'.now()->format('Y-m-d')));
    }

    /** @test */
    public function only_the_requested_rates_are_returned_from_the_keyless_table(): void
    {
        $requestBuilderMock = $this->requestBuilder(hasApiKey: false);
        $requestBuilderMock->expects('makeRequest')->once()->andReturn($this->keylessResponse());

        $rates = (new AllRatesTodayDriver($requestBuilderMock))->exchangeRate('EUR', ['GBP', 'USD']);

        $this->assertEquals(['GBP' => 0.86158, 'USD' => 1.1034], $rates);
    }

    /** @test */
    public function a_currency_missing_from_the_keyless_table_throws_an_exception(): void
    {
        $requestBuilderMock = $this->requestBuilder(hasApiKey: false);
        $requestBuilderMock->expects('makeRequest')->once()->andReturn($this->keylessResponse());

        $this->expectException(ExchangeRateException::class);
        $this->expectExceptionMessage('No exchange rate from EUR to NGN. An API key gives access to more currencies.');

        (new AllRatesTodayDriver($requestBuilderMock))->exchangeRate('EUR', ['GBP', 'NGN']);
    }

    /** @test */
    public function a_date_without_an_api_key_throws_an_exception_and_makes_no_request(): void
    {
        $requestBuilderMock = $this->requestBuilder(hasApiKey: false);
        $requestBuilderMock->expects('makeRequest')->never();

        $this->expectException(ExchangeRateException::class);
        $this->expectExceptionMessage('Historical exchange rates need an API key.');

        (new AllRatesTodayDriver($requestBuilderMock))->exchangeRate('EUR', 'GBP', now()->subDay());
    }

    /** @test */
    public function latest_rate_uses_the_keyed_endpoint_with_an_api_key(): void
    {
        $requestBuilderMock = $this->requestBuilder(hasApiKey: true);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/v1/rates', ['source' => 'EUR', 'target' => 'GBP']])
            ->once()
            ->andReturn($this->keyedResponse(['GBP' => 0.86158]));

        $rate = (new AllRatesTodayDriver($requestBuilderMock))->exchangeRate('EUR', 'GBP');

        $this->assertSame(0.86158, $rate);
    }

    /** @test */
    public function historical_rates_use_the_keyed_endpoint_with_an_api_key(): void
    {
        $date = now()->subDay();

        $requestBuilderMock = $this->requestBuilder(hasApiKey: true);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/v1/rates', [
                'source' => 'EUR',
                'target' => 'GBP,USD',
                'time' => $date->format('Y-m-d'),
            ]])
            ->once()
            ->andReturn($this->keyedResponse(['GBP' => 0.87053, 'USD' => 1.09]));

        $rates = (new AllRatesTodayDriver($requestBuilderMock))->exchangeRate('EUR', ['GBP', 'USD'], $date);

        $this->assertEquals(['GBP' => 0.87053, 'USD' => 1.09], $rates);
    }

    /** @test */
    public function cached_exchange_rate_is_returned_if_it_exists(): void
    {
        Cache::forever('laravel_xr_EUR_GBP_'.now()->format('Y-m-d'), 0.123456);

        $requestBuilderMock = $this->requestBuilder(hasApiKey: false);
        $requestBuilderMock->expects('makeRequest')->never();

        $rate = (new AllRatesTodayDriver($requestBuilderMock))->exchangeRate('EUR', 'GBP');

        $this->assertEquals(0.123456, $rate);
    }

    /** @test */
    public function the_same_currency_needs_no_request(): void
    {
        $requestBuilderMock = $this->requestBuilder(hasApiKey: false);
        $requestBuilderMock->expects('makeRequest')->never();

        $rate = (new AllRatesTodayDriver($requestBuilderMock))->exchangeRate('EUR', 'EUR', now()->subDay());

        $this->assertSame(1.0, $rate);
    }

    /** @test */
    public function exception_is_thrown_if_the_date_is_in_the_future(): void
    {
        $this->expectException(InvalidDateException::class);

        (new AllRatesTodayDriver($this->requestBuilder(hasApiKey: true)))
            ->exchangeRate('EUR', 'GBP', now()->addDay());
    }

    /** @test */
    public function exception_is_thrown_if_the_currency_is_invalid(): void
    {
        $this->expectException(InvalidCurrencyException::class);

        (new AllRatesTodayDriver($this->requestBuilder(hasApiKey: false)))->exchangeRate('INVALID', 'GBP');
    }

    private function requestBuilder(bool $hasApiKey): RequestBuilder&MockInterface
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->allows('hasApiKey')->andReturn($hasApiKey);

        return $requestBuilderMock;
    }

    private function keylessResponse(): Response
    {
        return new Response([
            'result' => 'success',
            'base_code' => 'EUR',
            'rates' => ['EUR' => 1, 'GBP' => 0.86158, 'USD' => 1.1034, 'CAD' => 1.4561],
        ]);
    }

    /**
     * @param  array<string, float>  $rates
     */
    private function keyedResponse(array $rates): Response
    {
        $rows = [];

        foreach ($rates as $target => $rate) {
            $rows[] = ['rate' => $rate, 'source' => 'EUR', 'target' => $target, 'time' => '2026-11-03T12:00:00+00:00'];
        }

        return new Response($rows);
    }
}
