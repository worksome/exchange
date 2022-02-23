<?php

declare(strict_types=1);

namespace Worksome\Exchange\Commands;

use Illuminate\Console\Command;
use Worksome\Exchange\Contracts\CurrencyCodeProvider;
use Worksome\Exchange\Exceptions\InvalidCurrencyCodeException;
use Worksome\Exchange\Exchange;
use Worksome\Exchange\Support\Rates;

use function Termwind\render;

final class ViewLatestRatesCommand extends Command
{
    public $signature = 'exchange:rates
        {base_currency? : The base currency to convert from.}
        {currencies?* : Any number of currencies to fetch exchange rates for.}';

    public $description = 'Retrieve exchange rates for a given set of currencies.';

    public function handle(Exchange $exchange, CurrencyCodeProvider $currencyCodeProvider): int
    {
        $data = $this->data($currencyCodeProvider);

        try {
            // @phpstan-ignore-next-line
            $this->renderRates($exchange->rates($data['base_currency'], $data['currencies']));
        } catch (InvalidCurrencyCodeException $exception) {
            render("<span class='px-2 py-1 bg-red-500 text-gray-50'>{$exception->getMessage()}</span>");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     */
    private function data(CurrencyCodeProvider $currencyCodeProvider): array
    {
        /** @var array<int, string> $givenCurrencies */
        $givenCurrencies = $this->argument('currencies');

        return [
            'base_currency' => $this->argument('base_currency') ?? $this->ask('Which base currency do you want to use?'),
            'currencies' => count($givenCurrencies) > 0 ? $givenCurrencies : $this->choice(
                'Which currencies do you want to fetch exchange rates for?',
                $currencyCodeProvider->all(),
                multiple: true,
            ),
        ];
    }

    private function renderRates(Rates $rates): void
    {
    }
}
