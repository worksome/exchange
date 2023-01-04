<?php

declare(strict_types=1);

namespace Worksome\Exchange;

use Worksome\Exchange\Contracts\Actions\ValidatesCurrencyCodes;
use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Exceptions\InvalidCurrencyCodeException;
use Worksome\Exchange\Support\Rates;
use Worksome\Exchange\Testing\FakeExchangeRateProvider;

final class Exchange
{
    public function __construct(
        private ValidatesCurrencyCodes $validateCurrencyCodes,
        private ExchangeRateProvider $exchangeRateProvider,
    ) {
    }

    /**
     * @param array<string, float> $rates
     */
    public function fake(array $rates = []): void
    {
        $this->exchangeRateProvider = (new FakeExchangeRateProvider())->defineRates($rates);
    }

    /**
     * @param non-empty-array<int, string> $currencies
     *
     * @throws InvalidCurrencyCodeException
     */
    public function rates(string $baseCurrency, array $currencies): Rates
    {
        ($this->validateCurrencyCodes)([$baseCurrency, ...$currencies]);

        return $this->exchangeRateProvider->getRates($baseCurrency, $currencies);
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->exchangeRateProvider->$name(...$arguments);
    }
}
