<?php

namespace AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRateHost;

use AshAllenDesign\LaravelExchangeRates\Interfaces\RequestSender;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ResponseContract;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class RequestBuilder implements RequestSender
{
    // TODO Use HTTPS if SSL option enabled.
    private const BASE_URL = 'http://api.exchangerate.host/';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('laravel-exchange-rates.api_key');
    }

    /**
     * Make an API request to the ExchangeRatesAPI.
     *
     * @param  string  $path
     * @param  array<string, string>  $queryParams
     * @return ResponseContract
     *
     * @throws RequestException
     */
    public function makeRequest(string $path, array $queryParams = []): ResponseContract
    {
        $rawResponse = Http::baseUrl(self::BASE_URL)
            ->get(
                $path,
                array_merge(['access_key' => $this->apiKey], $queryParams)
            )
            ->throw()
            ->json();

        return new Response($rawResponse);
    }
}
