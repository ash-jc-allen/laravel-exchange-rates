<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | Define which API service should be used to retrieve the exchange rates.
    |
    | Supported: "exchangeratesapi-legacy", "exchange-rates-data-api"
    |
    */
    'driver' => 'exchangeratesapi-legacy',

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Define your exchangeratesapi.io API key here.
    |
    */
    'api_key' => env('EXCHANGE_RATES_API_KEY'),

];
