<?php

return [
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
    ],
];
