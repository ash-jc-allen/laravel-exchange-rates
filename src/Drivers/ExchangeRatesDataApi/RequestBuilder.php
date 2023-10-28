<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesDataApi;

use AshAllenDesign\LaravelExchangeRates\Interfaces\RequestSender;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ResponseContract;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class RequestBuilder implements RequestSender
{
    private const BASE_URL = 'api.apilayer.com/exchangerates_data/';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('laravel-exchange-rates.api_key');
    }

    /**
     * Make an API request to the Exchange Rates Data API.
     *
     * @param  string  $path
     * @param  array<string, string>  $queryParams
     * @return ResponseContract
     *
     * @throws RequestException
     */
    public function makeRequest(string $path, array $queryParams = []): ResponseContract
    {
        $protocol = config('laravel-exchange-rates.https') ? 'https://' : 'http://';

        $rawResponse = Http::baseUrl($protocol.self::BASE_URL)
            ->withHeaders([
                'apiKey' => $this->apiKey,
            ])
            ->get($path, $queryParams)
            ->throw()
            ->json();

        return new Response($rawResponse);
    }
}
