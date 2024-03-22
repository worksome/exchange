<?php

declare(strict_types=1);

return [

    /**
     * Go ahead and select a default exchange driver to be used when
     * looking up exchange rates.
     *
     * Supported: 'null', 'fixer', 'exchange_rate', 'frankfurter', 'currency_geo', 'cache'
     */

    'default' => env('EXCHANGE_DRIVER', 'frankfurter'),

    'services' => [

        /*
        |--------------------------------------------------------------------------
        | Fixer.io
        |--------------------------------------------------------------------------
        |
        | Fixer is a paid service for converting currency codes. To use Fixer, you'll
        | need an API Access Key from the Fixer dashboard. Set that here, and then
        | change the 'default' to 'fixer' or set EXCHANGE_DRIVER to 'fixer'.
        |
        */

        'fixer' => [
            'access_key' => env('FIXER_ACCESS_KEY'),
        ],

        /*
        |--------------------------------------------------------------------------
        | ExchangeRate.host
        |--------------------------------------------------------------------------
        |
        | ExchangeRate is a paid service for converting currency codes. To use ExchangeRate, you'll
        | need an API Access Key from the ExchangeRate dashboard. Set that here, and then change
        | the 'default' to 'exchange_rate' or set EXCHANGE_DRIVER to 'exchange_rate'.
        |
        */

        'exchange_rate' => [
            'access_key' => env('EXCHANGE_RATE_ACCESS_KEY'),
        ],

        /*
        |--------------------------------------------------------------------------
        | CurrencyGeo.com
        |--------------------------------------------------------------------------
        |
        | CurrencyGeo is a paid service for converting currency codes. To use CurrencyGeo, you'll
        | need an API Access Key from the CurrencyGeo dashboard. Set that here, and then change
        | the 'default' to 'currency_geo' or set EXCHANGE_DRIVER to 'currency_geo'.
        |
        */

        'currency_geo' => [
            'access_key' => env('CURRENCY_GEO_ACCESS_KEY'),
        ],

        /*
        |--------------------------------------------------------------------------
        | Cache
        |--------------------------------------------------------------------------
        |
        | The cache driver is a decorator that will store rates retrieved from the
        | given strategy in your application cache for the specified timeout. By
        | default, we set the timeout to 24 hours, but you're free to alter it
        | to suit the needs of your app.
        |
        */

        'cache' => [
            'strategy' => env('EXCHANGE_RATES_CACHE_STRATEGY', 'frankfurter'),
            'ttl' => env('EXCHANGE_RATES_CACHE_TTL', 60 * 60 * 24), // 24 hours
            'key' => env('EXCHANGE_RATES_CACHE_KEY', 'cached_exchange_rates'),
            'store' => env('EXCHANGE_RATES_CACHE_STORE'),
        ],
    ],

    'features' => [

        /**
         * Laravel's about command provides useful information regarding the state of
         * your Laravel application. If `about_command` is set to true, we will
         * show useful information about exchange in about command output.
         */
        'about_command' => true,

    ]
];
