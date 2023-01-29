<?php

namespace AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApILegacy;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class RequestBuilder
{
    private const BASE_URL = 'https://api.exchangeratesapi.io/v1/';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('laravel-exchange-rates.api_key');
    }

    /**
     * Make an API request to the ExchangeRatesAPI.
     *
     * @param  string  $path
     * @param  string[]  $queryParams
     * @return mixed
     *
     * @throws RequestException
     */
    public function makeRequest(string $path, array $queryParams = []): mixed
    {
        return Http::baseUrl(self::BASE_URL)
            ->get($path, [
                'access_key' => $this->apiKey,
                ...$queryParams,
            ])
            ->throw()
            ->json();
    }
}
