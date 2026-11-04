<?php

namespace AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday;

use AshAllenDesign\LaravelExchangeRates\Interfaces\RequestSender;
use AshAllenDesign\LaravelExchangeRates\Interfaces\ResponseContract;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class RequestBuilder implements RequestSender
{
    private const BASE_URL = 'allratestoday.com/api';

    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('laravel-exchange-rates.api_key') ?: null;
    }

    /**
     * Whether an API key is configured. Without one, only the latest rates
     * from the keyless endpoint are available.
     */
    public function hasApiKey(): bool
    {
        return $this->apiKey !== null;
    }

    /**
     * Make an API request to the AllRatesToday API.
     *
     * @param  array<string, string>  $queryParams
     *
     * @throws RequestException
     */
    public function makeRequest(string $path, array $queryParams = []): ResponseContract
    {
        $protocol = config('laravel-exchange-rates.https') ? 'https://' : 'http://';

        $request = Http::baseUrl($protocol.self::BASE_URL);

        if ($this->apiKey !== null) {
            $request = $request->withToken($this->apiKey);
        }

        $rawResponse = $request
            ->get($path, $queryParams)
            ->throw()
            ->json();

        return new Response($rawResponse);
    }
}
