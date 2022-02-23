<?php

use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\ExchangeRateProviders\CachedProvider;
use Worksome\Exchange\ExchangeRateProviders\FixerProvider;
use Worksome\Exchange\ExchangeRateProviders\NullProvider;
use Worksome\Exchange\Support\ExchangeRateManager;

it('can instantiate all drivers', function (string $driver, string $expectedClass) {
    $manager = new ExchangeRateManager($this->app);

    expect($manager->driver($driver))
        ->toBeInstanceOf($expectedClass)
        ->toBeInstanceOf(ExchangeRateProvider::class);
})->with([
    ['null', NullProvider::class],
    ['fixer', FixerProvider::class],
    ['cache', CachedProvider::class],
]);

it('can instantiate the cache driver even if no key or ttl is given', function () {
    config()->set('exchange.services.cache.key', null);
    config()->set('exchange.services.cache.ttl', null);

    $manager = new ExchangeRateManager($this->app);

    expect($manager->driver('cache'))->toBeInstanceOf(CachedProvider::class);
});
