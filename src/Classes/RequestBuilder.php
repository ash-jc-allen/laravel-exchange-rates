<?php

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use GuzzleHttp\Client;

class RequestBuilder
{
    /**
     * The base URL for the Exchange Rates API.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * The API KEY for the Exchange Rates API.
     *
     * @var string
     */
    private $apiKey;

    /**
     * @var Client
     */
    private $client;

    /**
     * RequestBuilder constructor.
     *
     * @param  Client|null  $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->baseUrl = config('services.exchange_rates.base_url');
        $this->apiKey = config('services.exchange_rates.api_key');
    }

    /**
     * Make an API request to the ExchangeRatesAPI.
     *
     * @param  string  $path
     * @param  string[]  $queryParams
     *
     * @return mixed
     */
    public function makeRequest(string $path, array $queryParams = [])
    {
        $url = $this->baseUrl.$path.'?access_key=' . $this->apiKey;

        foreach ($queryParams as $param => $value) {
            $url .= '&'.urlencode($param).'='.urlencode($value);
        }

        return json_decode($this->client->get($url)->getBody()->getContents(), true);
    }
}
