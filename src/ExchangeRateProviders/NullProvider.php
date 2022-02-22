<?php

declare(strict_types=1);

namespace Worksome\Exchange\ExchangeRateProviders;

use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\Rates;

final class NullProvider implements ExchangeRateProvider
{
    public function getRates(string $baseCurrency, array $currencies): Rates
    {
        return new Rates(
            $baseCurrency,
            array_fill_keys($currencies, 1.0),
            now()
        );
    }
}
