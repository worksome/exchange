<?php

declare(strict_types=1);

namespace Worksome\Exchange;

use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Exchange\Actions\ValidateCurrencyCodes;
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

        $this->app->bind(ExchangeRateProvider::class, function (Application $app) {
            return (new ExchangeRateManager($app))->driver();
        });
        $this->app->bind(CurrencyCodeProvider::class, FlatCurrencyCodeProvider::class);
        $this->app->bind(ValidatesCurrencyCodes::class, ValidateCurrencyCodes::class);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('exchange')
            ->hasConfigFile()
            ->hasCommand(ViewLatestRatesCommand::class);
    }
}
