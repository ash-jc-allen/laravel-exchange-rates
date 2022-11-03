<?php

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;

class RequestBuilder
{
    /**
     * The base URL for the Exchange Rates API.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * The API key for the Exchange Rates API.
     *
     * @var string
     */
    private $apiKey;

    /**
     * RequestBuilder constructor.
     */
    public function __construct()
    {
        $this->baseUrl = config('laravel-exchange-rates.api_url');
        $this->apiKey = config('laravel-exchange-rates.api_key');
    }

    /**
     * Make an API request to the ExchangeRatesAPI.
     *
     * @param  string  $path
     * @param  string[]  $queryParams
     * @return mixed
     *
     * @throws GuzzleException
     */
    public function makeRequest(string $path, array $queryParams = [])
    {
        return Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->get($this->baseUrl.$path, $queryParams);
    }
}
