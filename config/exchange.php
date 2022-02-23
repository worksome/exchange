<?php

return [

    /**
     * Go ahead and select a default exchange driver to be used when
     * looking up exchange rates.
     *
     * Supported: 'null', 'fixer', 'cache'
     */
    'default' => env('EXCHANGE_DRIVER', 'null'),

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
            'strategy' => 'fixer',
            'ttl' => 60 * 60 * 24, // 24 hours
            'key' => 'cached_exchange_rates',
        ],
    ],
];
