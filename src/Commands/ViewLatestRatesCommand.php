<?php

declare(strict_types=1);

namespace Worksome\Exchange\Commands;

use Illuminate\Console\Command;

final class ViewLatestRatesCommand extends Command
{
    public $signature = 'exchange:latest
        {base_currency? : The base currency to convert from.}
        {currencies?* : Any number of currencies to retrieve exchange rates for.}';

    public $description = 'Retrieve exchange rates for a given set of currencies.';

    public function handle(): int
    {
        $data = $this->data();

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
}
