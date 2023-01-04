<?php

use Illuminate\Cache\ArrayStore;
use Illuminate\Contracts\Cache\Repository;
use Mockery as m;
use Worksome\Exchange\ExchangeRateProviders\CachedProvider;
use Worksome\Exchange\Support\Rates;
use Worksome\Exchange\Testing\FakeExchangeRateProvider;

it('calls the underlying strategy', function () {
    $cache = new \Illuminate\Cache\Repository(new ArrayStore());

    $fakeProvider = new FakeExchangeRateProvider();
    $fakeProvider->defineRates(['EUR' => 3.5]);

    $cachedProvider = new CachedProvider($cache, $fakeProvider, 'foo', 60);
    $rates = $cachedProvider->getRates('GBP', ['EUR']);

    $fakeProvider->assertRetrievedRates();
    expect($rates->getRates())->toBe(['EUR' => 3.5]);
});

it('caches the result relative to the given currencies for the given ttl', function () {
    $cache = m::mock(Repository::class);
    $cache->shouldReceive('remember')
        ->withSomeOfArgs('foo:GBP:EUR,USD', 60)
        ->once()
        ->andReturn(new Rates('GBP', ['EUR' => 4.2], now()));

    $cachedProvider = new CachedProvider($cache, new FakeExchangeRateProvider(), 'foo', 60);
    $rates = $cachedProvider->getRates('GBP', ['USD', 'EUR']);

    expect($rates->getRates())->toBe(['EUR' => 4.2]);
});

it('generates the cache key correctly', function (string $baseCurrency, array $currencies, string $expectedKey) {
    $cache = m::mock(Repository::class);
    $cache->shouldReceive('remember')
        ->withSomeOfArgs($expectedKey, 60)
        ->once()
        ->andReturn(new Rates('GBP', [], now()));

    $cachedProvider = new CachedProvider($cache, new FakeExchangeRateProvider(), 'foo', 60);
    $cachedProvider->getRates($baseCurrency, $currencies);
})->with([
    ['GBP', ['EUR'], 'foo:GBP:EUR'],
    ['GBP', ['EUR', 'USD'], 'foo:GBP:EUR,USD'],
    [
        'GBP',
        ['USD', 'EUR'],
        'foo:GBP:EUR,USD'
    ], // It should sort the order so that we don't make unnecessary requests just because the order was altered.
]);
