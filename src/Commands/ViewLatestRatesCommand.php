<?php

declare(strict_types=1);

namespace Worksome\Exchange\Commands;

use Illuminate\Console\Command;
use Worksome\Exchange\Exceptions\InvalidCurrencyCodeException;
use Worksome\Exchange\Exchange;
use Worksome\Exchange\Support\Rates;

use function Termwind\render;

final class ViewLatestRatesCommand extends Command
{
    public $signature = 'exchange:rates
        {base_currency? : The base currency to convert from.}
        {currencies?* : Any number of currencies to retrieve exchange rates for.}';

    public $description = 'Retrieve exchange rates for a given set of currencies.';

    public function handle(Exchange $exchange): int
    {
        $data = $this->data();

        try {
            $this->renderRates($exchange->rates($data['base_currency'], $data['currencies']));
        } catch (InvalidCurrencyCodeException $exception) {
            render("<span class='px-2 py-1 bg-red-500 text-gray-50'>{$exception->getMessage()}</span>");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @return array{base_currency: string, currencies: array<int, string>}
     */
    private function data(): array
    {
        return [
            'base_currency' => $this->argument('base_currency') ?? $this->ask('Which base currency do you want to use?'),
            'currencies' => $this->argument('currencies') ?? [],
        ];
    }

    private function renderRates(Rates $rates): void
    {
    }
}
