<?php

declare(strict_types=1);

namespace Worksome\Exchange\ExchangeRateProviders;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\Rates;

final readonly class CurrencyGEOProvider implements ExchangeRateProvider
{
    public function __construct(
        private Factory $client,
        private string $accessKey,
        private string $baseUrl = 'https://api.getgeoapi.com/v2',
    ) {
    }

    /**
     * @throws RequestException
     */
    public function getRates(string $baseCurrency, array $currencies): Rates
    {
        // If it's the same currency, return it.
        if (count($currencies) === 1 && $baseCurrency === $currencies[0]) {
            return new Rates(
                $baseCurrency,
                [$baseCurrency => 1],
                now()->startOfDay(),
            );
        }

        $data = $this->makeRequest($baseCurrency, $currencies);

        /**
         * @var non-empty-array<string, array{
         *      currency_name: string,
         *      rate: numeric-string,
         *      rate_for_amount: numeric-string
         * }> $rates
         */
        $rates = $data->get('rates');

        return new Rates(
            $baseCurrency,
            // @phpstan-ignore argument.type
            collect($rates)->map(fn (array $value) => floatval($value['rate']))->all(),
            now()->startOfDay(),
        );
    }

    /**
     * @param array<int, string> $currencies
     *
     * @return Collection<string, mixed>
     *
     * @throws RequestException
     */
    private function makeRequest(string $baseCurrency, array $currencies): Collection
    {
        return $this->client()
            ->get('/currency/convert', [
                'api_key' => $this->accessKey,
                'from' => $baseCurrency,
                'to' => implode(',', $currencies),
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
