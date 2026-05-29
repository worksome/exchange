<?php

use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Worksome\Exchange\ExchangeRateProviders\FrankfurterV2Provider;
use Worksome\Exchange\Support\Rates;

/**
 * The v2 API returns a list of rows shaped { date, base, quote, rate }.
 *
 * @return array<int, array{date: string, base: string, quote: string, rate: float|int}>
 */
function v2Rows(?string $date = null): array
{
    $date ??= now()->subDay()->format('Y-m-d');

    return [
        ['date' => $date, 'base' => 'EUR', 'quote' => 'EUR', 'rate' => 1], // int -> should be cast to float
        ['date' => $date, 'base' => 'EUR', 'quote' => 'GBP', 'rate' => 2.5],
    ];
}

it('is able to make a real call to the API', function () {
    $client = new Factory();
    $provider = new FrankfurterV2Provider($client);
    $rates = $provider->getRates('EUR', currencies());

    expect($rates)->toBeInstanceOf(Rates::class);
})->group('integration');

it('makes a HTTP request to the correct endpoint', function () {
    $client = new Factory();
    $client->fake(['*' => v2Rows()]);

    $provider = new FrankfurterV2Provider($client);
    $provider->getRates('EUR', currencies());

    $client->assertSent(function (Request $request) {
        return str_starts_with($request->url(), 'https://api.frankfurter.dev/v2/rates');
    });
});

it('maps the v2 row shape into a quote => rate array', function () {
    $client = new Factory();
    $client->fake(['*' => v2Rows()]);

    $provider = new FrankfurterV2Provider($client);
    $rates = $provider->getRates('EUR', currencies());

    expect($rates->rates)->toBe(['EUR' => 1.0, 'GBP' => 2.5]);
});

it('returns floats for all rates', function () {
    $client = new Factory();
    $client->fake(['*' => v2Rows()]);

    $provider = new FrankfurterV2Provider($client);
    $rates = $provider->getRates('EUR', currencies());

    expect($rates->rates)->each->toBeFloat();
});

it('sets the returned timestamp as the retrievedAt timestamp', function () {
    Carbon::setTestNow(now());

    $client = new Factory();
    $client->fake(['*' => v2Rows(now()->subDay()->format('Y-m-d'))]);

    $provider = new FrankfurterV2Provider($client);
    $rates = $provider->getRates('EUR', currencies());

    expect($rates->retrievedAt->format('Ymd'))->toBe(now()->subDay()->format('Ymd'));
});

it('makes a HTTP request to a custom base url', function () {
    $client = new Factory();
    $client->fake(['*' => v2Rows()]);

    $provider = new FrankfurterV2Provider($client, 'https://custom.frankfurter.dev/v2');
    $provider->getRates('EUR', currencies());

    $client->assertSent(function (Request $request) {
        return str_starts_with($request->url(), 'https://custom.frankfurter.dev/v2/rates');
    });
});

it('throws a RequestException if a 500 error occurs', function () {
    $client = new Factory();
    $client->fake(['*' => Create::promiseFor(new Response(500))]);

    $provider = new FrankfurterV2Provider($client);
    $provider->getRates('EUR', currencies());
})->throws(RequestException::class);
