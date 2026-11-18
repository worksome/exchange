<?php

use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Worksome\Exchange\ExchangeRateProviders\AllRatesTodayProvider;
use Worksome\Exchange\Support\Rates;

it('is able to make a real call to the API', function () {
    $client = new Factory();
    $allRatesTodayProvider = new AllRatesTodayProvider($client, getenv('ALL_RATES_TODAY_ACCESS_KEY'));
    $rates = $allRatesTodayProvider->getRates('EUR', currencies());

    expect($rates)->toBeInstanceOf(Rates::class);
})
    ->skip(
        in_array(getenv('ALL_RATES_TODAY_ACCESS_KEY'), [false, ''], true),
        'No ALL_RATES_TODAY_ACCESS_KEY was defined.'
    )
    ->group('integration');

it('makes a HTTP request to the correct endpoint', function () {
    $client = new Factory();
    $client->fake(['*' => [
        ['source' => 'EUR', 'target' => 'GBP', 'rate' => 0.85, 'time' => '2026-10-22T12:00:00Z'],
    ]]);

    $allRatesTodayProvider = new AllRatesTodayProvider($client, 'password');
    $allRatesTodayProvider->getRates('EUR', ['GBP', 'USD']);

    $client->assertSent(function (Request $request) {
        return str_starts_with($request->url(), 'https://allratestoday.com/api/v1/rates')
            && $request['source'] === 'EUR'
            && $request['target'] === 'GBP,USD'
            && $request->hasHeader('Authorization', 'Bearer password');
    });
});

it('returns floats keyed by target currency', function () {
    $client = new Factory();
    $client->fake(['*' => [
        ['source' => 'EUR', 'target' => 'EUR', 'rate' => 1, 'time' => '2026-10-22T12:00:00Z'],
        ['source' => 'EUR', 'target' => 'GBP', 'rate' => '0.85', 'time' => '2026-10-22T12:00:00Z'],
    ]]);

    $allRatesTodayProvider = new AllRatesTodayProvider($client, 'password');
    $rates = $allRatesTodayProvider->getRates('EUR', currencies());

    expect($rates->rates)
        ->toBe(['EUR' => 1.0, 'GBP' => 0.85])
        ->each->toBeFloat();
});

it('sets the returned time as the retrievedAt timestamp', function () {
    $client = new Factory();
    $client->fake(['*' => [
        ['source' => 'EUR', 'target' => 'GBP', 'rate' => 0.85, 'time' => '2026-10-22T12:00:00Z'],
    ]]);

    $allRatesTodayProvider = new AllRatesTodayProvider($client, 'password');
    $rates = $allRatesTodayProvider->getRates('EUR', currencies());

    expect($rates->retrievedAt->timestamp)->toBe(Carbon::parse('2026-10-22T12:00:00Z')->timestamp);
});

it('throws a RequestException if a 401 error occurs', function () {
    $client = new Factory();
    $client->fake(['*' => Create::promiseFor(new Response(401))]);

    $allRatesTodayProvider = new AllRatesTodayProvider($client, 'password');
    $allRatesTodayProvider->getRates('EUR', currencies());
})->throws(RequestException::class);
