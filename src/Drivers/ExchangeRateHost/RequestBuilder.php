<?php

namespace AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRateHost;

use AshAllenDesign\LaravelExchangeRates\Drivers\ExchangeRatesApiIo\Response;
use AshAllenDesign\LaravelExchangeRates\Interfaces\RequestSender;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ResponseContract;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class RequestBuilder implements RequestSender
{
    private const BASE_URL = 'https://api.exchangerate.host/';

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
            ->get($path, $queryParams)
            ->throw()
            ->json();

        return new Response($rawResponse);
    }
}
