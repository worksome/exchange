<?php

declare(strict_types=1);

namespace Worksome\Exchange\Support;

use Illuminate\Http\Client\Factory;
use Illuminate\Support\Manager;
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
            // @phpstan-ignore-next-line
            $this->config->get('exchange.services.fixer'),
        );
    }
}
