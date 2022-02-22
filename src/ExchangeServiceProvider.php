<?php

declare(strict_types=1);

namespace Worksome\Exchange;

use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Exchange\Commands\ViewLatestRatesCommand;
use Worksome\Exchange\Contracts\ExchangeRateProvider;
use Worksome\Exchange\Support\ExchangeRateManager;

final class ExchangeServiceProvider extends PackageServiceProvider
{
    public function registeringPackage(): void
    {
        $this->app->bind(ExchangeRateProvider::class, function (Application $app) {
            return (new ExchangeRateManager($app))->driver();
        });
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('exchange')
            ->hasConfigFile()
            ->hasCommand(ViewLatestRatesCommand::class);
    }
}
