<?php

use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Exceptions\InvalidConfigurationException;
use Worksome\Exchange\ExchangeRateProviders\CachedProvider;
use Worksome\Exchange\ExchangeRateProviders\FixerProvider;
use Worksome\Exchange\ExchangeRateProviders\NullProvider;
use Worksome\Exchange\Support\ExchangeRateManager;

beforeEach(function () {
    config()->set('exchange.services.fixer.access_key', 'password');
});

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

it('will throw the right exception if no fixer API key has been configured', function () {
    config()->set('exchange.services.fixer.access_key', null);

    $manager = new ExchangeRateManager($this->app);
    $manager->driver('fixer');
})->throws(InvalidConfigurationException::class, 'You haven\'t set up an API key for Fixer!');
