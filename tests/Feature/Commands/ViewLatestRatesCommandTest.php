<?php

declare(strict_types=1);

it('asks for a base currency if one is not provided', function () {
    $this
        ->artisan('exchange:rates', ['currencies' => ['GBP', 'USD']])
        ->expectsQuestion('Which base currency do you want to use?', 'EUR');
});

it('fails if an invalid base currency is passed', function () {
    $this
        ->artisan('exchange:rates', ['base_currency' => 'FOO', 'currencies' => ['GBP', 'USD']])
        ->assertFailed();
});
