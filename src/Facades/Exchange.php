<?php

declare(strict_types=1);

namespace Worksome\Exchange\Facades;

use Illuminate\Support\Facades\Facade;
use Worksome\Exchange\Testing\FakeExchangeRateProvider;

/**
 * @see \Worksome\Exchange\Exchange
 */
final class Exchange extends Facade
{
    public static function fake(): void
    {
        self::swap(new FakeExchangeRateProvider());
    }

    protected static function getFacadeAccessor(): string
    {
        return 'exchange';
    }
}
