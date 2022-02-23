<?php

declare(strict_types=1);

namespace Worksome\Exchange\Support;

use Illuminate\Cache\Repository;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Manager;
use Worksome\Exchange\ExchangeRateProviders\CachedProvider;
use Worksome\Exchange\ExchangeRateProviders\FixerProvider;
use Worksome\Exchange\ExchangeRateProviders\NullProvider;

final class ExchangeRateManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return strval($this->config->get('exchange.default') ?? 'null');
    }

    public function createNullDriver(): NullProvider
    {
        return new NullProvider();
    }

    public function createFixerDriver(): FixerProvider
    {
        return new FixerProvider(
            // @phpstan-ignore-next-line
            $this->container->make(Factory::class),
            strval($this->config->get('exchange.services.fixer.access_key')),
        );
    }

    public function createCacheDriver(): CachedProvider
    {
        return new CachedProvider(
            // @phpstan-ignore-next-line
            $this->container->make(Repository::class),
            // @phpstan-ignore-next-line
            $this->driver($this->config->get('exchange.services.cache.strategy')),
            strval($this->config->get('exchange.services.cache.key', 'cached_exchange_rates')),
            intval($this->config->get('exchange.services.cache.ttl', 60 * 60 * 24)),
        );
    }
}
