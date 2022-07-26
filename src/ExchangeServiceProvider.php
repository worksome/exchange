<?php

declare(strict_types=1);

namespace Worksome\Exchange;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Exchange\Actions\ValidateCurrencyCodes;
use Worksome\Exchange\Commands\InstallCommand;
use Worksome\Exchange\Commands\ViewLatestRatesCommand;
use Worksome\Exchange\Contracts\Actions\ValidatesCurrencyCodes;
use Worksome\Exchange\Contracts\CurrencyCodeProvider;
use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\ExchangeRateManager;
use Worksome\Exchange\Support\FlatCurrencyCodeProvider;

final class ExchangeServiceProvider extends PackageServiceProvider
{
    public function registeringPackage(): void
    {
        $this->app->singleton('exchange', Exchange::class);

        $this->app->bind(
            ExchangeRateProvider::class,
            fn (Application $app) => (new ExchangeRateManager($app))->driver()
        );

        $this->app->bind(CurrencyCodeProvider::class, FlatCurrencyCodeProvider::class);
        $this->app->bind(ValidatesCurrencyCodes::class, ValidateCurrencyCodes::class);

        $this->extendAboutCommand();
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('exchange')
            ->hasConfigFile()
            ->hasCommands(
                InstallCommand::class,
                ViewLatestRatesCommand::class
            );
    }

    private function extendAboutCommand(): void
    {
        if (! class_exists(AboutCommand::class)) {
            return;
        }

        if (! config('exchange.features.about_command', true)) {
            return;
        }

        AboutCommand::add('Exchange', [
            'Driver' => fn () => config('exchange.default'),
        ]);
    }
}
