<?php

declare(strict_types=1);

namespace Worksome\Exchange\Facades;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Worksome\Exchange\Support\Rates;

/**
 * @method static Rates  rates(string $baseCurrency, array $currencies)
 * @method        static assertRetrievedRates(int $times = 1)
 *
 * @see \Worksome\Exchange\Exchange
 */
final class Exchange extends Facade
{
    /**
     * @param array<string, float> $rates
     */
    public static function fake(array $rates = []): void
    {
        assert(self::$app instanceof Application);

        /**
         * @var \Worksome\Exchange\Exchange $fake
         *
         * @phpstan-ignore larastanStrictRules.noFacadeRule
         */
        $fake = self::$app->instance(\Worksome\Exchange\Exchange::class, self::getFacadeRoot());

        $fake->fake($rates);
    }

    protected static function getFacadeAccessor(): string
    {
        return 'exchange';
    }
}
