<?php

declare(strict_types=1);

use Worksome\Exchange\Contracts\CurrencyCodeProvider;
use Worksome\Exchange\Facades\Exchange;

it('asks for a base currency if one is not provided', function () {
    $this
        ->artisan('exchange:rates', ['currencies' => ['GBP', 'USD']])
        ->expectsQuestion('Which base currency do you want to use?', 'EUR');
});

it('asks for currencies if none are provided', function () {
    $this
        ->artisan('exchange:rates', ['base-currency' => 'GBP'])
        ->expectsChoice(
            'Which currencies do you want to fetch exchange rates for?',
            ['EUR', 'USD'],
            $this->app->make(CurrencyCodeProvider::class)->all(),
        );
});

it('fails if an invalid base currency is passed', function () {
    $this
        ->artisan('exchange:rates', ['base-currency' => 'FOO', 'currencies' => ['GBP', 'USD']])
        ->assertFailed();
});

it('fails if an invalid conversion currency is passed', function () {
    $this
        ->artisan('exchange:rates', ['base-currency' => 'GBP', 'currencies' => ['FOO', 'USD']])
        ->assertFailed();
});

it('retrieves rates using the default provider', function () {
    Exchange::fake(['GBP' => 1.2]);

    $this
        ->artisan('exchange:rates', ['base-currency' => 'GBP', 'currencies' => ['EUR', 'USD']])
        ->assertSuccessful();

    Exchange::assertRetrievedRates();
});
