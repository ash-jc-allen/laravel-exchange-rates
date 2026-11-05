<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\AllRatesToday;

use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\AllRatesTodayDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\Response;
use AshAllenDesign\LaravelExchangeRates\Exceptions\ExchangeRateException;
use AshAllenDesign\LaravelExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Carbon\Carbon;
use Mockery;
use Mockery\MockInterface;

final class ExchangeRateBetweenDateRangeTest extends TestCase
{
    /** @test */
    public function rates_for_one_currency_are_keyed_by_date(): void
    {
        $requestBuilderMock = $this->requestBuilder(hasApiKey: true);
        $requestBuilderMock->expects('makeRequest')
            ->withArgs(['/v1/rates', [
                'source' => 'EUR',
                'target' => 'GBP',
                'from' => '2026-10-01',
                'to' => '2026-10-02',
                'group' => 'day',
            ]])
            ->once()
            ->andReturn($this->series('GBP', ['2026-10-02' => 0.87, '2026-10-01' => 0.86]));

        $rates = (new AllRatesTodayDriver($requestBuilderMock))
            ->exchangeRateBetweenDateRange('EUR', 'GBP', Carbon::parse('2026-10-01'), Carbon::parse('2026-10-02'));

        $this->assertSame(['2026-10-01' => 0.86, '2026-10-02' => 0.87], $rates);
    }

    /** @test */
    public function rates_for_many_currencies_are_requested_one_at_a_time(): void
    {
        $requestBuilderMock = $this->requestBuilder(hasApiKey: true);
        $requestBuilderMock->expects('makeRequest')
            ->twice()
            ->andReturnUsing(fn (string $path, array $params): Response => $params['target'] === 'GBP'
                ? $this->series('GBP', ['2026-10-01' => 0.86])
                : $this->series('USD', ['2026-10-01' => 1.1]));

        $rates = (new AllRatesTodayDriver($requestBuilderMock))
            ->exchangeRateBetweenDateRange('EUR', ['GBP', 'USD'], Carbon::parse('2026-10-01'), Carbon::parse('2026-10-01'));

        $this->assertSame(['2026-10-01' => ['GBP' => 0.86, 'USD' => 1.1]], $rates);
    }

    /** @test */
    public function a_date_range_without_an_api_key_throws_an_exception_and_makes_no_request(): void
    {
        $requestBuilderMock = $this->requestBuilder(hasApiKey: false);
        $requestBuilderMock->expects('makeRequest')->never();

        $this->expectException(ExchangeRateException::class);

        (new AllRatesTodayDriver($requestBuilderMock))
            ->exchangeRateBetweenDateRange('EUR', 'GBP', Carbon::parse('2026-10-01'), Carbon::parse('2026-10-02'));
    }

    /** @test */
    public function the_same_currency_needs_no_api_key(): void
    {
        $requestBuilderMock = $this->requestBuilder(hasApiKey: false);
        $requestBuilderMock->expects('makeRequest')->never();

        $rates = (new AllRatesTodayDriver($requestBuilderMock))
            ->exchangeRateBetweenDateRange('EUR', 'EUR', Carbon::parse('2026-10-01'), Carbon::parse('2026-10-02'));

        $this->assertEquals(['2026-10-01' => 1.0, '2026-10-02' => 1.0], $rates);
    }

    /** @test */
    public function exception_is_thrown_if_the_start_date_is_after_the_end_date(): void
    {
        $this->expectException(InvalidDateException::class);

        (new AllRatesTodayDriver($this->requestBuilder(hasApiKey: true)))
            ->exchangeRateBetweenDateRange('EUR', 'GBP', Carbon::parse('2026-10-02'), Carbon::parse('2026-10-01'));
    }

    private function requestBuilder(bool $hasApiKey): RequestBuilder&MockInterface
    {
        $requestBuilderMock = Mockery::mock(RequestBuilder::class);
        $requestBuilderMock->allows('hasApiKey')->andReturn($hasApiKey);

        return $requestBuilderMock;
    }

    /**
     * @param  array<string, float>  $rates
     */
    private function series(string $target, array $rates): Response
    {
        $rows = [];

        foreach ($rates as $date => $rate) {
            $rows[] = ['rate' => $rate, 'source' => 'EUR', 'target' => $target, 'time' => $date.'T23:59:59+00:00'];
        }

        return new Response($rows);
    }
}
