<?php

use Carbon\Carbon;
use Worksome\Exchange\Support\Rates;

it('can retrieve the base currency', function () {
    $rates = new Rates('EUR', ['GBP' => 1.2, 'USD' => 1.1], now());

    expect($rates->baseCurrency)->toBe('EUR');
});

it('can retrieve all given rates', function () {
    $rates = new Rates('EUR', ['GBP' => 1.2, 'USD' => 1.1], now());

    expect($rates->rates)->toBe(['GBP' => 1.2, 'USD' => 1.1]);
});

it('can retrieve the retrievedAt time', function () {
    Carbon::setTestNow(now());
    $rates = new Rates('EUR', ['GBP' => 1.2, 'USD' => 1.1], now());

    expect($rates->retrievedAt->equalTo(now()))->toBeTrue();
});
