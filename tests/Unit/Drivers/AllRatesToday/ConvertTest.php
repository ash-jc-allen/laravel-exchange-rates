<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\AllRatesToday;

use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\AllRatesTodayDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\Response;
use AshAllenDesign\LaravelExchangeRates\Exceptions\ExchangeRateException;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Carbon\Carbon;
use Mockery;

final class ConvertTest extends TestCase
{
    /** @test */
    public function value_is_converted_with_the_latest_rate_without_an_api_key(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->allows('hasApiKey')->andReturnFalse();
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/open/latest/EUR'])
            ->once()
            ->andReturn(new Response(['rates' => ['GBP' => 0.5, 'USD' => 2]]));

        $driver = new AllRatesTodayDriver($requestBuilderMock);

        $this->assertEquals(50.0, $driver->convert(100, 'EUR', 'GBP'));
    }

    /** @test */
    public function value_is_converted_for_many_currencies(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->allows('hasApiKey')->andReturnFalse();
        $requestBuilderMock->expects('makeRequest')
            ->once()
            ->andReturn(new Response(['rates' => ['GBP' => 0.5, 'USD' => 2]]));

        $driver = new AllRatesTodayDriver($requestBuilderMock);

        $this->assertEquals(['GBP' => 50.0, 'USD' => 200.0], $driver->convert(100, 'EUR', ['GBP', 'USD']));
    }

    /** @test */
    public function converting_on_a_date_without_an_api_key_throws_an_exception(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->allows('hasApiKey')->andReturnFalse();
        $requestBuilderMock->expects('makeRequest')->never();

        $this->expectException(ExchangeRateException::class);

        (new AllRatesTodayDriver($requestBuilderMock))->convert(100, 'EUR', 'GBP', Carbon::parse('2026-10-01'));
    }
}
