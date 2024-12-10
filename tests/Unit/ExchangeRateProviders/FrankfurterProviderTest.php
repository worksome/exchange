<?php

use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Worksome\Exchange\ExchangeRateProviders\FrankfurterProvider;
use Worksome\Exchange\Support\Rates;

it('is able to make a real call to the API', function () {
    $client = new Factory();
    $fixerProvider = new FrankfurterProvider($client);
    $rates = $fixerProvider->getRates('EUR', currencies());

    expect($rates)->toBeInstanceOf(Rates::class);
})->group('integration');

it('makes a HTTP request to the correct endpoint', function () {
    $client = new Factory();
    $client->fake(['*' => [
        'date' => now()->subDay()->format('Y-m-d'),
        'rates' => [
            'EUR' => 1, // Even though this is an int, it should be converted to a float
            'GBP' => 2.5,
        ],
    ]]);

    $fixerProvider = new FrankfurterProvider($client);
    $fixerProvider->getRates('EUR', currencies());

    $client->assertSent(function (Request $request) {
        return str_starts_with($request->url(), 'https://api.frankfurter.dev/v1/latest');
    });
});

it('returns floats for all rates', function () {
    $client = new Factory();
    $client->fake(['*' => [
        'date' => now()->subDay()->format('Y-m-d'),
        'rates' => [
            'EUR' => 1, // Even though this is an int, it should be converted to a float
            'GBP' => 2.5,
        ],
    ]]);

    $fixerProvider = new FrankfurterProvider($client);
    $rates = $fixerProvider->getRates('EUR', currencies());

    expect($rates->getRates())->each->toBeFloat();
});

it('sets the returned timestamp as the retrievedAt timestamp', function () {
    Carbon::setTestNow(now());

    $client = new Factory();
    $client->fake(['*' => [
        'date' => now()->subDay()->format('Y-m-d'),
        'rates' => [],
    ]]);

    $fixerProvider = new FrankfurterProvider($client);
    $rates = $fixerProvider->getRates('EUR', currencies());

    expect($rates->getRetrievedAt()->format('Ymd'))->toBe(now()->subDay()->format('Ymd'));
});

it('throws a RequestException if a 500 error occurs', function () {
    $client = new Factory();
    $client->fake(['*' => Create::promiseFor(new Response(500))]);

    $fixerProvider = new FrankfurterProvider($client);
    $fixerProvider->getRates('EUR', currencies());
})->throws(RequestException::class);
