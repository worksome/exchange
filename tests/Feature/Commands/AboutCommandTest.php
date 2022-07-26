<?php

declare(strict_types=1);

use Illuminate\Foundation\Console\AboutCommand;

it('shows exchange details', function () {
    $this->artisan('about')
        ->assertSuccessful()
        ->expectsOutputToContain('Exchange')
        ->expectsOutputToContain('Driver');
})->skip(! class_exists(AboutCommand::class));
