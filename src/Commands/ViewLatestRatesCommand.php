<?php

declare(strict_types=1);

namespace Worksome\Exchange\Commands;

use Illuminate\Console\Command;
use Illuminate\View\Compilers\BladeCompiler;
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
        {base-currency? : The base currency to convert from.}
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
            'base_currency' => $this->argument('base-currency') ?? $this->ask(
                'Which base currency do you want to use?'
            ),
            'currencies' => count($givenCurrencies) > 0 ? $givenCurrencies : $this->choice(
                'Which currencies do you want to fetch exchange rates for?',
                $currencyCodeProvider->all(),
                multiple: true,
            ),
        ];
    }

    private function renderRates(Rates $rates): void
    {
        render(BladeCompiler::render(<<<'HTML'
            <div class="mx-2 mt-1 space-y-1">
                <header class="w-full max-w-90 text-center py-1 bg-green-500 font-bold text-gray-50">
                    Exchange rates based on 1 {{ $baseCurrency }}
                </header>
                <div class="max-w-90">
                    <div class="flex justify-between text-gray">
                        <span>Currency</span>
                        <span>Exchange Rate</span>
                    </div>
                    @foreach ($rates as $currency => $amount)
                        <div class="flex space-x-1">
                            <span>{{ $currency }}</span>
                            <span class="flex-1 text-gray content-repeat-['.']"></span>
                            <span class="text-yellow">{{ $amount }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        HTML, ['baseCurrency' => $rates->getBaseCurrency(), 'rates' => $rates->getRates()]));
    }
}
