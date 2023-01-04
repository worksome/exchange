<?php

declare(strict_types=1);

namespace Worksome\Exchange\ExchangeRateProviders;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Arr;
use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\Rates;

final class CachedProvider implements ExchangeRateProvider
{
    public function __construct(
        private Repository $cache,
        private ExchangeRateProvider $strategy,
        private string $key,
        private int $ttl,
    ) {
    }

    public function getRates(string $baseCurrency, array $currencies): Rates
    {
        $currenciesForKey = implode(',', Arr::sort($currencies));

        return $this->cache->remember(
            "{$this->key}:{$baseCurrency}:{$currenciesForKey}",
            $this->ttl,
            fn () => $this->strategy->getRates($baseCurrency, $currencies),
        );
    }
}
