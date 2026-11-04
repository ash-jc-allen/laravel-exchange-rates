<?php

namespace AshAllenDesign\LaravelExchangeRates\Drivers\AllRatesToday;

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

    /**
     * The keyless endpoint answers with a `rates` map, and the keyed endpoint
     * with a list of `{source, target, rate, time}` rows.
     *
     * @return array<string, float>
     */
    public function rates(): array
    {
        return $this->get('rates') ?? array_column($this->rawResponse, 'rate', 'target');
    }

    /**
     * The rows of a keyed date range for one target, keyed by date.
     *
     * @return array<string, float>
     */
    public function timeSeries(): array
    {
        $rates = [];

        foreach ($this->rawResponse as $row) {
            $rates[substr($row['time'], 0, 10)] = $row['rate'];
        }

        return $rates;
    }

    public function raw(): mixed
    {
        return $this->rawResponse;
    }
}
