<?php

declare(strict_types=1);

namespace Worksome\Exchange\ExchangeRateProviders;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\Rates;

final class FixerProvider implements ExchangeRateProvider
{
    public function __construct(
        private Factory $client,
        private string $accessKey,
        private string $baseUrl = 'https://data.fixer.io/api',
    ) {
    }

    /**
     * @throws RequestException
     */
    public function getRates(string $baseCurrency, array $currencies): Rates
    {
        $data = $this->makeRequest($baseCurrency, $currencies);

        return new Rates(
            $baseCurrency,
            // @phpstan-ignore-next-line
            collect($data->get('rates'))->map(fn(mixed $value) => floatval($value))->all(),
            CarbonImmutable::createFromTimestamp(intval($data->get('timestamp')))
        );
    }

    /**
     * @param array<int, string> $currencies
     * @return Collection<string, mixed>
     * @throws RequestException
     */
    private function makeRequest(string $baseCurrency, array $currencies): Collection
    {
        return $this->client()
            ->get('/latest', [
                'access_key' => $this->accessKey,
                'base' => $baseCurrency,
                'format' => 1,
                'symbols' => implode(',', $currencies),
            ])
            ->throw()
            ->collect();
    }

    private function client(): PendingRequest
    {
        return $this->client
            ->baseUrl($this->baseUrl)
            ->asJson()
            ->acceptJson();
    }
}
