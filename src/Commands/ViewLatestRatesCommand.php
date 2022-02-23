<?php

declare(strict_types=1);

namespace Worksome\Exchange\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Blade;
use Worksome\Exchange\Commands\Concerns\HasUsefulConsoleMethods;
use Worksome\Exchange\Contracts\CurrencyCodeProvider;
use Worksome\Exchange\Exceptions\InvalidCurrencyCodeException;
use Worksome\Exchange\Exchange;
use Worksome\Exchange\Support\Rates;

use function Termwind\render;

final class ViewLatestRatesCommand extends Command
{
    use HasUsefulConsoleMethods;

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
            $this->newLine();
            $this->failure($exception->getMessage());
            $this->newLine();

            return self::FAILURE;
        }

        $this->askUserToStarRepository();

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
        render(Blade::render('
        <div>
            <div class="my-1 w-full py-1 text-center bg-green-500 text-gray-50">
                Exchange rates based on 1 {{ $baseCurrency }}
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Currency</th>
                        <th>Exchange Rate</th>
                    </tr>
                </thead>
                @foreach($rates as $currency => $amount)
                <tr>
                    <th class="underline">{{ $currency }}</th>
                    <td>{{ $amount }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        ', ['baseCurrency' => $rates->getBaseCurrency(), 'rates' => $rates->getRates()]));
    }
}
