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

final readonly class FrankfurterProvider implements ExchangeRateProvider
{
    public function __construct(
        private Factory $client,
        private string $baseUrl = 'https://api.frankfurter.dev/v1',
    ) {
    }

    /**
     * @throws RequestException
     */
    public function getRates(string $baseCurrency, array $currencies): Rates
    {
        $data = $this->makeRequest($baseCurrency, $currencies);

        /** @var non-empty-array<string, float> $rates */
        $rates = $data->get('rates');

        return new Rates(
            $baseCurrency,
            // @phpstan-ignore argument.type
            collect($rates)->map(fn (mixed $value) => (float) $value)->all(),
            $this->getRetrievedAt($data),
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
    private function getRetrievedAt(Collection $data): CarbonImmutable
    {
        $date = $data->get('date');

        if (! is_string($date)) {
            throw new InvalidArgumentException('The returned date could not be parsed.');
        }

        $carbonInstance = CarbonImmutable::createFromFormat('Y-m-d', $date);

        if ($carbonInstance === null) {
            throw new InvalidArgumentException('The returned date could not be parsed.');
        }

        return $carbonInstance->timezone('Europe/Amsterdam')->setTime(16, 0, 0);
    }
}
