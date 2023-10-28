<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Tests\Unit\Drivers\ExchangeRatesDataApi;

use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesDataApi\RequestBuilder;
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
        $url = 'https://api.apilayer.com/exchangerates_data/latest?base=USD';

        Http::fake([
            $url => Http::response(['RESPONSE']),
            '*' => Http::response('SHOULD NOT HIT THIS!', 500),
        ]);

        $requestBuilder = new RequestBuilder();
        $requestBuilder->makeRequest('latest', ['base' => 'USD']);

        Http::assertSent(static function (Request $request) use ($url): bool {
            return $request->method() === 'GET'
                && $request->url() === $url
                && $request->hasHeader('apiKey', 'API-KEY');
        });
    }

    /** @test */
    public function request_protocol_respects_https_config_option(): void
    {
        config(['laravel-exchange-rates.https' => false]);

        $url = 'http://api.apilayer.com/exchangerates_data/latest?base=USD';

        Http::fake([
            $url => Http::response(['RESPONSE']),
            '*' => Http::response('SHOULD NOT HIT THIS!', 500),
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

        $url = 'https://api.apilayer.com/exchangerates_data/latest?base=USD';

        Http::fake([
            $url => Http::response(['RESPONSE'], 401),
            '*' => Http::response('SHOULD NOT HIT THIS!', 500),
        ]);

        $requestBuilder = new RequestBuilder();
        $requestBuilder->makeRequest('latest', ['base' => 'USD']);
    }
}
