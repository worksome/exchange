<?php

declare(strict_types=1);

namespace Worksome\Exchange\Contracts;

use Worksome\Exchange\Support\Rates;

interface ExchangeRateProvider
{
    /** @param non-empty-list<string> $currencies */
    public function getRates(string $baseCurrency, array $currencies): Rates;
}
