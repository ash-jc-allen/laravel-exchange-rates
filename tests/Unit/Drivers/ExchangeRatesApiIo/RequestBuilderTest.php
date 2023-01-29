<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\ExchangeRatesApiIo;

use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\RequestBuilder;
use AshAllenDesign\LaravelExchangeRates\Tests\Unit\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

final class RequestBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['laravel-exchange-rates.api_key' => 'API-KEY']);
    }

    /** @test */
    public function request_can_be_made_successfully(): void
    {
        Http::preventStrayRequests();

        $url = 'https://api.exchangeratesapi.io/v1/latest?access_key=API-KEY&base=USD';

        Http::fake([
            $url => Http::response(['RESPONSE'])
        ]);

        $requestBuilder = new RequestBuilder();
        $requestBuilder->makeRequest('latest', ['base' => 'USD']);

        Http::assertSent(static function (Request $request) use ($url): bool {
            return $request->method() === 'GET'
                && $request->url() === $url;
        });
    }

    /** @test */
    public function exception_is_thrown_if_the_request_fails(): void
    {
        $this->expectException(RequestException::class);

        Http::preventStrayRequests();

        $url = 'https://api.exchangeratesapi.io/v1/latest?access_key=API-KEY&base=USD';

        Http::fake([
            $url => Http::response(['RESPONSE'], 401)
        ]);

        $requestBuilder = new RequestBuilder();
        $requestBuilder->makeRequest('latest', ['base' => 'USD']);
    }
}
