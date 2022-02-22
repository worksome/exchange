<?php

declare(strict_types=1);

namespace Worksome\Exchange\Support;

use Carbon\CarbonInterface;

final class Rates
{
    /**
     * @param non-empty-array<string, float> $rates
     */
    public function __construct(
        private string $baseCurrency,
        private array $rates,
        private CarbonInterface $retrievedAt,
    ) {
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    /**
     * @return non-empty-array<string, float>
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    public function getRetrievedAt(): CarbonInterface
    {
        return $this->retrievedAt;
    }
}
