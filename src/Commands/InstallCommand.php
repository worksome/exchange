<?php

declare(strict_types=1);

namespace Worksome\Exchange\Commands;

use Illuminate\Console\Command;
use Worksome\Exchange\Commands\Concerns\HasUsefulConsoleMethods;

final class InstallCommand extends Command
{
    use HasUsefulConsoleMethods;

    protected $signature = 'exchange:install';

    protected $description = 'Publish Exchange\'s config file to your project.';

    public function handle(): int
    {
        $this->call('vendor:publish', ['--tag' => 'exchange-config']);

        $this->information('Alright, Exchange is installed! Try it out with `php artisan exchange:rates`.');

        return self::SUCCESS;
    }
}
