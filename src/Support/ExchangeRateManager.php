<?php

declare(strict_types=1);

namespace Worksome\Exchange\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Manager;
use Worksome\Exchange\ExchangeRateProviders\FixerProvider;
use Worksome\Exchange\ExchangeRateProviders\NullProvider;

final class ExchangeRateManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('exchange.default') ?? 'null';
    }

    public function createNullDriver(): NullProvider
    {
        return new NullProvider();
    }

    public function createFixerDriver(): FixerProvider
    {
        return new FixerProvider(
            $this->container->make(PendingRequest::class),
            $this->config->get('exchange.services.fixer'),
        );
    }
}
