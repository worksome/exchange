<?php

use Carbon\Carbon;
use Worksome\Exchange\Support\Rates;

it('can retrieve the base currency', function () {
    $rates = new Rates('EUR', ['GBP' => 1.2, 'USD' => 1.1], now());

    expect($rates->getBaseCurrency())->toBe('EUR');
});

it('can retrieve all given rates', function () {
    $rates = new Rates('EUR', ['GBP' => 1.2, 'USD' => 1.1], now());

    expect($rates->getRates())->toBe(['GBP' => 1.2, 'USD' => 1.1]);
});

it('can retrieve the retrievedAt time', function () {
    Carbon::setTestNow(now());
    $rates = new Rates('EUR', ['GBP' => 1.2, 'USD' => 1.1], now());

    expect($rates->getRetrievedAt()->equalTo(now()))->toBeTrue();
});
