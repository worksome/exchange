<?php

declare(strict_types=1);

namespace Worksome\Exchange\ExchangeRateProviders;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\Rates;

final class FrankfurterProvider implements ExchangeRateProvider
{
    public function __construct(
        private Factory $client,
        private string $baseUrl = 'https://api.frankfurter.app',
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
            collect($data->get('rates'))->map(fn (mixed $value) => (float) $value)->all(),
            $this->getRetreivedAt($data),
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
            ->get('/latest', [
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

    /**
     * @param Collection<string, mixed> $data
     *
     * @return CarbonImmutable
     */
    private function getRetreivedAt(Collection $data): CarbonImmutable
    {
        $date = $data->get('date');

        if (! is_string($date)) {
            throw new InvalidArgumentException('The returned date could not be parsed.');
        }

        $carbonInstance = CarbonImmutable::createFromFormat('Y-m-d', $date);

        if ($carbonInstance === false) {
            throw new InvalidArgumentException('The returned date could not be parsed.');
        }

        return $carbonInstance->timezone('Europe/Amsterdam')->setTime(16, 0, 0);
    }
}
