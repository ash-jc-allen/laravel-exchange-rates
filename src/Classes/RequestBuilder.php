<?php

namespace AshAllenDesign\LaravelExchangeRates\Classes;

use GuzzleHttp\Client;

class RequestBuilder
{
    private const BASE_URL = 'https://api.exchangeratesapi.io';

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
    }

    /**
     * Make an API request to the ExchangeRatesAPI.
     *
     * @param  string  $path
     * @param  array  ...$queryParams
     *
     * @return mixed
     */
    public function makeRequest(string $path, array $queryParams = [])
    {
        $url = self::BASE_URL.$path.'?';

        foreach ($queryParams as $param => $value) {
            $url .= '&'.urlencode($param).'='.urlencode($value);
        }

        return json_decode($this->client->get($url)->getBody()->getContents(), true);
    }
}
