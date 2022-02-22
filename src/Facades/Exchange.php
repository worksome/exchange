<?php

declare(strict_types=1);

namespace Worksome\Exchange\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static assertRetrievedRates(int $times = 1)
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
        self::$app->instance(
            \Worksome\Exchange\Exchange::class,
            self::getFacadeRoot()
        )->fake($rates);
    }

    protected static function getFacadeAccessor(): string
    {
        return 'exchange';
    }
}
