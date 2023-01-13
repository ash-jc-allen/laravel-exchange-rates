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
    'api_url' => env('EXCHANGE_RATES_API_URL', 'https://api.apilayer.com/exchangerates_data'),

    /*
    |--------------------------------------------------------------------------
    | API Parameter
    |--------------------------------------------------------------------------
    |
    | Define your the API key parameter here.
    | api.exchangeratesapi.io requires the key to be access_key
    | api.apilayer.com requires the key to be apikey
    |
    */
    'api_param' => env('EXCHANGE_RATES_API_PARAM', 'apikey'),

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
