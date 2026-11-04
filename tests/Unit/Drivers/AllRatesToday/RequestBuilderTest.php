<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\AllRatesToday;

use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\AllRatesTodayDriver;
use AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

final class RequestBuilderTest extends TestCase
{
    /** @test */
    public function no_authorization_header_is_sent_without_an_api_key(): void
    {
        config(['laravel-exchange-rates.api_key' => null]);

        $url = 'https://allratestoday.com/api/open/latest/USD';

        Http::fake([
            $url => Http::response(['rates' => ['EUR' => 0.9]]),
            '*' => Http::response('SHOULD NOT HIT THIS!', 500),
        ]);

        $requestBuilder = new RequestBuilder();
        $requestBuilder->makeRequest('/open/latest/USD');

        $this->assertFalse($requestBuilder->hasApiKey());

        Http::assertSent(static function (Request $request) use ($url): bool {
            return $request->method() === 'GET'
                && $request->url() === $url
                && ! $request->hasHeader('Authorization');
        });
    }

    /** @test */
    public function an_empty_api_key_counts_as_no_api_key(): void
    {
        config(['laravel-exchange-rates.api_key' => '']);

        $this->assertFalse((new RequestBuilder())->hasApiKey());
    }

    /** @test */
    public function the_api_key_is_sent_as_a_bearer_token(): void
    {
        config(['laravel-exchange-rates.api_key' => 'API-KEY']);

        $url = 'https://allratestoday.com/api/v1/rates?source=USD&target=EUR';

        Http::fake([
            $url => Http::response([['rate' => 0.9, 'target' => 'EUR']]),
            '*' => Http::response('SHOULD NOT HIT THIS!', 500),
        ]);

        $requestBuilder = new RequestBuilder();
        $requestBuilder->makeRequest('/v1/rates', ['source' => 'USD', 'target' => 'EUR']);

        $this->assertTrue($requestBuilder->hasApiKey());

        Http::assertSent(static function (Request $request) use ($url): bool {
            return $request->url() === $url
                && $request->hasHeader('Authorization', 'Bearer API-KEY');
        });
    }

    /** @test */
    public function request_protocol_respects_https_config_option(): void
    {
        config(['laravel-exchange-rates.https' => false]);

        $url = 'http://allratestoday.com/api/open/latest/USD';

        Http::fake([
            $url => Http::response(['rates' => ['EUR' => 0.9]]),
            '*' => Http::response('SHOULD NOT HIT THIS!', 500),
        ]);

        (new RequestBuilder())->makeRequest('/open/latest/USD');

        Http::assertSent(static fn (Request $request): bool => $request->url() === $url);
    }

    /** @test */
    public function exception_is_thrown_if_the_request_fails(): void
    {
        $this->expectException(RequestException::class);

        Http::fake(['*' => Http::response('ERROR!', 403)]);

        (new RequestBuilder())->makeRequest('/v1/rates', ['source' => 'USD']);
    }

    /** @test */
    public function the_driver_can_be_resolved_by_name(): void
    {
        $driver = app(ExchangeRate::class)->driver('all-rates-today');

        $this->assertInstanceOf(AllRatesTodayDriver::class, $driver);
    }
}
