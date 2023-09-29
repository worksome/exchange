<?php

declare(strict_types=1);

namespace Worksome\Exchange\Support;

use Illuminate\Cache\Repository;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Manager;
use Worksome\Exchange\Exceptions\InvalidConfigurationException;
use Worksome\Exchange\ExchangeRateProviders\CachedProvider;
use Worksome\Exchange\ExchangeRateProviders\ExchangeRateHostProvider;
use Worksome\Exchange\ExchangeRateProviders\FixerProvider;
use Worksome\Exchange\ExchangeRateProviders\FrankfurterProvider;
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
        $apiKey = $this->config->get('exchange.services.fixer.access_key');

        throw_unless(is_string($apiKey), new InvalidConfigurationException(
            'You haven\'t set up an API key for Fixer!'
        ));

        return new FixerProvider(
            $this->container->make(Factory::class),
            $apiKey,
        );
    }

    public function createExchangeRateDriver(): ExchangeRateHostProvider
    {
        $apiKey = $this->config->get('exchange.services.exchange_rate.access_key');

        throw_unless(is_string($apiKey), new InvalidConfigurationException(
            'You haven\'t set up an API key for ExchangeRate!'
        ));

        return new ExchangeRateHostProvider(
            $this->container->make(Factory::class),
            $apiKey,
        );
    }

    public function createFrankfurterDriver(): FrankfurterProvider
    {
        return new FrankfurterProvider(
            $this->container->make(Factory::class),
        );
    }

    public function createCacheDriver(): CachedProvider
    {
        return new CachedProvider(
            $this->container->make(Repository::class),
            // @phpstan-ignore-next-line
            $this->driver($this->config->get('exchange.services.cache.strategy')),
            strval($this->config->get('exchange.services.cache.key', 'cached_exchange_rates')),
            intval($this->config->get('exchange.services.cache.ttl', 60 * 60 * 24)),
        );
    }
}
