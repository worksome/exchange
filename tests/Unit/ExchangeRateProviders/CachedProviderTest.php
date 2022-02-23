<?php

use Illuminate\Cache\ArrayStore;
use Illuminate\Contracts\Cache\Repository;
use Worksome\Exchange\ExchangeRateProviders\CachedProvider;
use Worksome\Exchange\Support\Rates;
use Worksome\Exchange\Testing\FakeExchangeRateProvider;
use Mockery as m;

it('calls the underlying strategy', function () {
    $cache = new \Illuminate\Cache\Repository(new ArrayStore());

    $fakeProvider = new FakeExchangeRateProvider();
    $fakeProvider->defineRates(['EUR' => 3.5]);

    $cachedProvider = new CachedProvider($cache, $fakeProvider, 'foo', 60);
    $rates = $cachedProvider->getRates('GBP', ['EUR']);

    $fakeProvider->assertRetrievedRates();
    expect($rates->getRates())->toBe(['EUR' => 3.5]);
});

it('caches the result relative to the base currency for the given ttl', function () {
    $cache = m::mock(Repository::class);
    $cache->shouldReceive('remember')
        ->withSomeOfArgs('foo:GBP', 60)
        ->once()
        ->andReturn(new Rates('GBP', ['EUR' => 4.2], now()));

    $cachedProvider = new CachedProvider($cache, new FakeExchangeRateProvider(), 'foo', 60);
    $rates = $cachedProvider->getRates('GBP', ['EUR']);

    expect($rates->getRates())->toBe(['EUR' => 4.2]);
});
