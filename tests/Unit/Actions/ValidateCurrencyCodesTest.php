<?php

declare(strict_types=1);

use Worksome\Exchange\Actions\ValidateCurrencyCodes;
use Worksome\Exchange\Exceptions\InvalidCurrencyCodeException;
use Worksome\Exchange\Support\FlatCurrencyCodeProvider;

it('throws an exception if the currency code does not exist', function (string $currency) {
    $validateCurrencyCodes = new ValidateCurrencyCodes(new FlatCurrencyCodeProvider());
    $validateCurrencyCodes([$currency]);
})
    ->with([
        'FOO',
        'BAR',
        'BAZ',
        'GBPPP',
    ])
    ->throws(InvalidCurrencyCodeException::class);

it('does not throw an exception if all given currency codes exist', function () {
    $validateCurrencyCodes = new ValidateCurrencyCodes(new FlatCurrencyCodeProvider());
    $currencies = $validateCurrencyCodes(currencies());

    expect($currencies)->toBe(currencies());
});
