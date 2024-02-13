<?php

namespace AshAllenDesign\LaravelExchangeRates\Drivers\CurrencyBeacon;

use AshAllenDesign\LaravelExchangeRates\Interfaces\ResponseContract;

class Response implements ResponseContract
{
    public function __construct(private array $rawResponse)
    {
    }

    public function get(string $key): mixed
    {
        return data_get($this->rawResponse, $key);
    }

    public function rates(): array
    {
        return $this->get('response.rates');
    }

    public function timeSeries(): array
    {
        return $this->get('response');
    }

    public function raw(): mixed
    {
        return $this->rawResponse;
    }
}
