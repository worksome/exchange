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

final readonly class AllRatesTodayProvider implements ExchangeRateProvider
{
    public function __construct(
        private Factory $client,
        private string $accessKey,
        private string $baseUrl = 'https://allratestoday.com/api/v1',
    ) {
    }

    /**
     * @throws RequestException
     */
    public function getRates(string $baseCurrency, array $currencies): Rates
    {
        $data = $this->makeRequest($baseCurrency, $currencies);

        $time = $data->pluck('time')->filter()->first();

        return new Rates(
            $baseCurrency,
            // @phpstan-ignore argument.type
            $data->mapWithKeys(fn (array $value) => [$value['target'] => floatval($value['rate'])])->all(),
            is_string($time) ? CarbonImmutable::parse($time) : now()->startOfDay(),
        );
    }

    /**
     * @param array<int, string> $currencies
     *
     * @return Collection<int, array{source: string, target: string, rate: numeric, time?: string}>
     *
     * @throws RequestException
     */
    private function makeRequest(string $baseCurrency, array $currencies): Collection
    {
        return $this->client()
            ->get('/rates', [
                'source' => $baseCurrency,
                'target' => implode(',', $currencies),
            ])
            ->throw()
            ->collect();
    }

    private function client(): PendingRequest
    {
        return $this->client
            ->baseUrl($this->baseUrl)
            ->withToken($this->accessKey)
            ->acceptJson();
    }
}
