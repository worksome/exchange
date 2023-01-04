<?php

namespace Worksome\Exchange\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Console\Output\BufferedOutput;
use Termwind\Termwind;
use Worksome\Exchange\ExchangeServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Termwind::renderUsing(new BufferedOutput());

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Worksome\\Exchange\\Database\\Factories\\' . class_basename(
                $modelName
            ) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ExchangeServiceProvider::class,
        ];
    }
}
