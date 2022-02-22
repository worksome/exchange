<?php

declare(strict_types=1);

it('asks for a base currency if one is not provided', function () {
    $this
        ->artisan('exchange:latest', ['currencies' => ['GBP', 'USD']])
        ->expectsQuestion('Which base currency do you want to use?', 'EUR');
});
