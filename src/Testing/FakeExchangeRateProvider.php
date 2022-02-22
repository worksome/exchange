<?php

declare(strict_types=1);

namespace Worksome\Exchange\Testing;

use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\Rates;

final class FakeExchangeRateProvider implements ExchangeRateProvider
{
    /**
     * @var array<string, float>
     */
    private array $predefinedRates = [];

    public function getRates(string $baseCurrency, array $currencies): Rates
    {
        return new Rates($baseCurrency, $this->getRatesArray($currencies), now());
    }

    /**
     * @param array<string, float> $rates
     */
    public function defineRates(array $rates): self
    {
        $this->predefinedRates = array_merge($this->predefinedRates, $rates);

        return $this;
    }

    /**
     * @param non-empty-array<int, string> $currencies
     * @return non-empty-array<string, float>
     */
    private function getRatesArray(array $currencies): array
    {
        // @phpstan-ignore-next-line
        return array_merge(array_fill_keys($currencies, 1.0), $this->predefinedRates);
    }
}
