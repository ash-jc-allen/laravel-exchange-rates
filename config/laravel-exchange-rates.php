<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | Define the URL for the API.
    |
    */
    'api_url' => env('EXCHANGE_RATES_API_URL', 'https://api.apilayer.com/exchangerates_data/'),

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
