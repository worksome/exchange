# Exchange

Check exchange rates for any currency in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/worksome/exchange.svg?style=flat-square)](https://packagist.org/packages/worksome/exchange)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/worksome/exchange/tests.yml?branch=main&style=flat-square&label=Tests)](https://github.com/worksome/exchange/actions?query=workflow%3ATests+branch%3Amain)
[![GitHub Static Analysis Action Status](https://img.shields.io/github/actions/workflow/status/worksome/exchange/static.yml?branch=main&style=flat-square&label=Static%20Analysis)](https://github.com/worksome/exchange/actions?query=workflow%3A"Static%20Analysis"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/worksome/exchange.svg?style=flat-square)](https://packagist.org/packages/worksome/exchange)

If your app supports multi-currency, you'll no doubt need to check exchange rates. There are many third party services
to accomplish this, but why bother reinventing the wheel when we've done all the hard work for you?

Exchange provides an abstraction layer for exchange rate APIs, with a full suite of tools for caching, testing and local
development.

## Installation

You can install the package via composer.

```bash
composer require worksome/exchange
```

To install the exchange config file, you can use our `install` artisan command!

```bash
php artisan exchange:install
```

Exchange is now installed!

## Usage

Exchange ships with a number of useful drivers for retrieving exchange rates. The default is `exchange_rate`,
which is a free service, but you're welcome to change that to suit you app's requirements.

The driver can be set using the `EXCHANGE_DRIVER` environment variable. Supported values are: `null`, `fixer`, `exchange_rate` and `cache`.
Let's take a look at each of the options available.

### Null

You can start using Exchange locally with the `null` driver. This will simply return `1.0` for every exchange rate, which is generally fine for local development.

```php
use Worksome\Exchange\Facades\Exchange;

$exchangeRates = Exchange::rates('USD', ['GBP', 'EUR']);
```

In the example above, we are retrieving exchange rates for GBP and EUR based on USD. The `rates` method will return a `Worksome\Exchange\Support\Rates` object,
which includes the base currency, retrieved rates and the time of retrieval. Retrieved rates are an `array` with currency codes as keys and exchange rates as values.

```php
$rates = $exchangeRates->getRates(); // ['GBP' => 1.0, 'EUR' => 1.0]
```

### Fixer

Of course, the `null` driver isn't very useful when you want actual exchange rates. For this, you should use the `fixer` driver.

In your `exchange.php` config file, set `default` to `fixer`, or set `EXCHANGE_DRIVER` to `fixer` in your `.env` file.
Next, you'll need an access key from [https://fixer.io/dashboard](https://fixer.io/dashboard). Set `FIXER_ACCESS_KEY` to your provided
access key from Fixer.

That's it! Fixer is now configured as the default driver and running `Exchange::rates()` again will make a request to
Fixer for up-to-date exchange rates.

### ExchangeRate.host

[exchangerate.host](https://exchangerate.host) is an alternative to Fixer with an identical API spec.

In your `exchange.php` config file, set `default` to `exchange_rate`, or set `EXCHANGE_DRIVER` to `exchange_rate` in your `.env` file.
Set `EXCHANGE_RATE_ACCESS_KEY` to your provided access key from exchangerate.host.

With that task completed, you're ready to start using [exchangerate.host](https://exchangerate.host) for retrieving up-to-date
exchange rates.

### Currency.GetGeoApi.com

[Currency.GetGeoApi.com](https://currency.getgeoapi.com) is an alternative option you can use with a free quota.

In your `exchange.php` config file, set `default` to `currency_geo`, or set `EXCHANGE_DRIVER` to `currency_geo` in your `.env` file.
Set `CURRENCY_GEO_ACCESS_KEY` to your provided access key from currency.getgeoapi.com.

With that task completed, you're ready to start using [Currency.GetGeoApi.com](https://currency.getgeoapi.com) for retrieving up-to-date
exchange rates.

### Frankfurter.app

[frankfurter.app](https://frankfurter.app) is an open-source API for current and historical foreign exchange rates published by the European Central Bank, which can be used without an API key.

In your `exchange.php` config file, set `default` to `frankfurter`, or set `EXCHANGE_DRIVER` to `frankfurter` in your `.env` file.

With that task completed, you're ready to start using [frankfurter.app](https://frankfurter.app) for retrieving up-to-date
exchange rates.

### Cache

It's unlikely that you want to make a request to a third party service every time you call `Exchange::rates()`. To remedy
this, we provide a cache decorator that can be used to store retrieved exchange rates for a specified period (24 hours by default).

In your `exchange.php` config file, set `default` to `cache`, or set `EXCHANGE_DRIVER` to `cache` in your `.env` file.
You'll also want to pick a strategy under `services.cache.strategy`. By default, this will be `fixer`, but you are free to change that.
The strategy is the service that will be used to perform the exchange rate lookup when nothing is found in the cache.

There is also the option to override the `ttl` (how many seconds rates are cached for), `key` for your cached rates, and the `store`.

## Artisan

We provide an Artisan command for you to check Exchange is working correctly in your project.

```bash
php artisan exchange:rates USD GBP EUR
```

In the example above, exchange rates will be retrieved and displayed in the console from a base of USD to GBP and EUR respectively. You can add as many currencies as you'd like to the command.

<img width="1040" alt="CleanShot 2022-02-23 at 13 10 55@2x" src="https://user-images.githubusercontent.com/12202279/155325937-70c296d1-33be-484d-bcd1-bee3085dc592.png">

## Testing

To help you write tests using Exchange, we provide a fake implementation via the `Exchange::fake()` method.

```php
it('retrieves exchange rates', function () {
    Exchange::fake(['GBP' => 1.25, 'USD' => 1.105]);
    
    $this->get(route('my-app-route'))
        ->assertOk();
        
    Exchange::assertRetrievedRates();
});
```

The `assertRetrievedRates` method will cause your test to fail if no exchange rates were ever retrieved.

Internally, Exchange prides itself on a thorough test suite written in Pest, strict static analysis, and a very high level of code coverage. You may run these tests yourself by cloning the project and running our test script:

```bash
composer test
```

## Changelog

Please see [GitHub Releases](https://github.com/worksome/exchange/releases) for more information on what has changed recently.

## Credits

- [Luke Downing](https://github.com/lukeraymonddowning)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
