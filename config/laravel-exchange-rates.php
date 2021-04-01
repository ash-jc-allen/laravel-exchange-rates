<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | Define the URL for the API. Please note that the exchangeratesapi.io
    | API only allow HTTPS requests if you are using a paid account. So,
    | if you are using a free account, please make sure that your URL
    | begins with 'http://'.
    |
    */
    'api_url' => env('EXCHANGE_RATES_API_URL', 'http://api.exchangeratesapi.io/v1/'),

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
