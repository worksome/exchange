<?php

declare(strict_types=1);

namespace Worksome\Exchange\ExchangeRateProviders;

use Illuminate\Http\Client\Factory;
use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\Rates;

final class ExchangeRateHostProvider implements ExchangeRateProvider
{
    private FixerProvider $fixerProvider;

    public function __construct(private Factory $client, private string $accessKey)
    {
        $this->fixerProvider = new FixerProvider($this->client, $this->accessKey, 'https://api.exchangerate.host');
    }

    public function getRates(string $baseCurrency, array $currencies): Rates
    {
        return $this->fixerProvider->getRates($baseCurrency, $currencies);
    }
}
