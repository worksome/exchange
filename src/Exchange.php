<?php

declare(strict_types=1);

namespace Worksome\Exchange;

use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\Rates;

final class Exchange
{
    public function __construct(
        private ExchangeRateProvider $exchangeRateProvider,
    )
    {
    }

    /**
     * @param non-empty-array<int, string> $currencies
     */
    public function rates(string $baseCurrency, array $currencies): Rates
    {
        return $this->exchangeRateProvider->getRates($baseCurrency, $currencies);
    }

}
