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

final class ConvertBetweenDateRangeTest extends TestCase
{
    /** @test */
    public function values_are_converted_for_each_date_with_an_api_key(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->allows('hasApiKey')->andReturnTrue();
        $requestBuilderMock->expects('makeRequest')
            ->once()
            ->andReturn(new Response([
                ['rate' => 0.5, 'source' => 'EUR', 'target' => 'GBP', 'time' => '2026-10-01T23:59:59+00:00'],
                ['rate' => 0.25, 'source' => 'EUR', 'target' => 'GBP', 'time' => '2026-10-02T23:59:59+00:00'],
            ]));

        $converted = (new AllRatesTodayDriver($requestBuilderMock))
            ->convertBetweenDateRange(100, 'EUR', 'GBP', Carbon::parse('2026-10-01'), Carbon::parse('2026-10-02'));

        $this->assertEquals(['2026-10-01' => 50.0, '2026-10-02' => 25.0], $converted);
    }

    /** @test */
    public function converting_a_date_range_without_an_api_key_throws_an_exception(): void
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->allows('hasApiKey')->andReturnFalse();
        $requestBuilderMock->expects('makeRequest')->never();

        $this->expectException(ExchangeRateException::class);

        (new AllRatesTodayDriver($requestBuilderMock))
            ->convertBetweenDateRange(100, 'EUR', 'GBP', Carbon::parse('2026-10-01'), Carbon::parse('2026-10-02'));
    }
}
