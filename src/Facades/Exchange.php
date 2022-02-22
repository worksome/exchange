<?php

declare(strict_types=1);

namespace Worksome\Exchange\Facades;

use Illuminate\Support\Facades\Facade;
use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Testing\FakeExchangeRateProvider;

/**
 * @see \Worksome\Exchange\Exchange
 */
final class Exchange extends Facade
{
    /**
     * Fake the ExchangeRateProvider, optionally providing a set of fake rates
     * to use.
     *
     * @param array<string, float> $rates
     */
    public static function fake(array $rates = []): void
    {
        self::$app->instance(
            ExchangeRateProvider::class,
            (new FakeExchangeRateProvider())->defineRates($rates)
        );
    }

    protected static function getFacadeAccessor(): string
    {
        return 'exchange';
    }
}
