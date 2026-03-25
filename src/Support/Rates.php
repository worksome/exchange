<?php

declare(strict_types=1);

namespace Worksome\Exchange\Support;

use Carbon\CarbonInterface;
use Deprecated;

final readonly class Rates
{
    /** @param non-empty-array<string, float> $rates */
    public function __construct(
        public string $baseCurrency,
        public array $rates,
        public CarbonInterface $retrievedAt,
    ) {
    }

    #[Deprecated('Use `baseCurrency` property instead.', since: '2.4.0')]
    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    /** @return non-empty-array<string, float> */
    #[Deprecated('Use `rates` property instead.', since: '2.4.0')]
    public function getRates(): array
    {
        return $this->rates;
    }

    #[Deprecated('Use `retrievedAt` property instead.', since: '2.4.0')]
    public function getRetrievedAt(): CarbonInterface
    {
        return $this->retrievedAt;
    }
}
