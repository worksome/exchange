<?php

declare(strict_types=1);

namespace Worksome\Exchange\Testing;

use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\Rates;

final class FakeExchangeRateProvider implements ExchangeRateProvider
{

    public function getRates(string $baseCurrency, array $currencies): Rates
    {
        // TODO: Implement getRates() method.
    }
}
