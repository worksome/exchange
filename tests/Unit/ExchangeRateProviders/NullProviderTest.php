<?php

use Carbon\Carbon;
use Worksome\Exchange\ExchangeRateProviders\NullProvider;

it('sets the given base currency as the base currency', function (string $currency) {
    Carbon::setTestNow(now());

    $nullProvider = new NullProvider();
    $rates = $nullProvider->getRates($currency, currencies());

    expect($rates->getBaseCurrency())->toBe($currency);
})->with('currencies');

it('always returns 1.0 for any currency', function (string $currency) {
    $nullProvider = new NullProvider();
    $rates = $nullProvider->getRates($currency, currencies());

    expect($rates->getRates())->each->toBeFloat()->toBe(1.0);
})->with('currencies');

it('sets now as the retrievedAt timestamp', function () {
    Carbon::setTestNow(now());

    $nullProvider = new NullProvider();
    $rates = $nullProvider->getRates('EUR', currencies());

    expect($rates->getRetrievedAt()->equalTo(now()))->toBeTrue();
});
