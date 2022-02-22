<?php

declare(strict_types=1);

namespace Worksome\Exchange\Support;

use Illuminate\Support\Manager;
use Worksome\Exchange\ExchangeRateProviders\NullProvider;

final class ExchangeRateManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return 'null';
    }

    public function createNullDriver(): NullProvider
    {
        return new NullProvider();
    }
}
