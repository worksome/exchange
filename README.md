# Exchange

Check exchange rates for any currency in Laravel

[![Tests](https://github.com/worksome/exchange/actions/workflows/run-tests.yml/badge.svg)](https://github.com/worksome/exchange/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/worksome/exchange/actions/workflows/phpstan.yml/badge.svg)](https://github.com/worksome/exchange/actions/workflows/phpstan.yml)

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

You can start using Exchange right away with the `null` driver, which comes preconfigured. This will simply return `1.0` for every exchange rate, which is generally fine for local development.

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

### ExchangeRate

[exchangerate.host](https://exchangerate.host/) is a free alternative to Fixer with an identical API spec. You 
don't even need an API key! 

In your `exchange.php` config file, set `default` to `fixer`, or set `EXCHANGE_DRIVER` to `fixer` in your `.env` file.

With that task completed, you're ready to start using [exchangerate.host](https://exchangerate.host/) for retrieving up-to-date
exchange rates.

### Cache

It's unlikely that you want to make a request to a third party service every time you call `Exchange::rates()`. To remedy
this, we provide a cache decorator that can be used to store retrieved exchange rates for a specified period (24 hours by default).

In your `exchange.php` config file, set `default` to `cache`, or set `EXCHANGE_DRIVER` to `cache` in your `.env` file.
You'll also want to pick a strategy under `services.cache.strategy`. By default, this will be `fixer`, but you are free to change that.
The strategy is the service that will be used to perform the exchange rate lookup when nothing is found in the cache.

There is also the option to override the `ttl` (how many seconds rates are cached for) and `key` for your cached rates.

## Artisan

We provide an Artisan command for you to check Exchange is working correctly in your project.

```bash
php artisan exchange:rates USD GBP EUR
```

In the example above, exchange rates will be retrieved and displayed in the console from a base of USD to GBP and EUR respectively. You can add as many currencies as you'd like to the command.

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

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Luke Downing](https://github.com/lukeraymonddowning)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
