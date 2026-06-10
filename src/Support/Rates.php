<?php

declare(strict_types=1);

namespace Worksome\Exchange\Support;

use Carbon\CarbonInterface;

final readonly class Rates
{
    /** @param non-empty-array<string, float> $rates */
    public function __construct(
        public string $baseCurrency,
        public array $rates,
        public CarbonInterface $retrievedAt,
    ) {
    }
}
