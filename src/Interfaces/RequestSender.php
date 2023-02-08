<?php

declare(strict_types=1);

namespace AshAllenDesign\LaravelExchangeRates\Interfaces;

use Illuminate\Http\Client\RequestException;

interface RequestSender
{
    /**
     * Make an API request to the specified driver's API.
     *
     * @param  string  $path
     * @param  string[]  $queryParams
     * @return mixed
     *
     * @throws RequestException
     */
    public function makeRequest(string $path, array $queryParams = []): mixed;
}
