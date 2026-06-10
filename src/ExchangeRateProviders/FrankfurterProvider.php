<?php

declare(strict_types=1);

namespace Worksome\Exchange\ExchangeRateProviders;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use InvalidArgumentException;
use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\Rates;

/**
 * Frankfurter provider, targeting the v2 API.
 *
 * Rates are requested from `/rates` with `base` / `quotes` query parameters
 * and returned as a list of rows shaped `{ date, base, quote, rate }`. The v2
 * API covers a much larger currency set than the ECB-only v1 (e.g. AED, MAD)
 * and requires no API key.
 */
final readonly class FrankfurterProvider implements ExchangeRateProvider
{
    public function __construct(
        private Factory $client,
        private string $baseUrl = 'https://api.frankfurter.dev/v2',
    ) {
    }

    /**
     * @throws RequestException
     */
    public function getRates(string $baseCurrency, array $currencies): Rates
    {
        /** @var list<array{date: string, base: string, quote: string, rate: float|int}> $rows */
        $rows = $this->client()
            ->get('/rates', [
                'base' => $baseCurrency,
                'quotes' => implode(',', $currencies),
            ])
            ->throw()
            ->json();

        $rates = [];

        foreach ($rows as $row) {
            $rates[strval($row['quote'])] = floatval($row['rate']);
        }

        /** @var non-empty-array<string, float> $rates */
        return new Rates(
            $baseCurrency,
            $rates,
            $this->getRetrievedAt($rows[0]['date'] ?? null),
        );
    }

    private function client(): PendingRequest
    {
        return $this->client
            ->baseUrl($this->baseUrl)
            ->asJson()
            ->acceptJson();
    }

    private function getRetrievedAt(string|null $date): CarbonImmutable
    {
        if ($date === null) {
            throw new InvalidArgumentException('The returned date could not be parsed.');
        }

        // The leading "!" resets the time to 00:00:00 so the date cannot roll
        // over when the instance is converted to the Europe/Amsterdam timezone.
        $carbonInstance = CarbonImmutable::createFromFormat('!Y-m-d', $date);

        if ($carbonInstance === null) {
            throw new InvalidArgumentException('The returned date could not be parsed.');
        }

        return $carbonInstance->timezone('Europe/Amsterdam')->setTime(16, 0, 0);
    }
}
