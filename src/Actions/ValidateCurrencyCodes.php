<?php

declare(strict_types=1);

namespace Worksome\Exchange\Actions;

use Worksome\Exchange\Contracts\Actions\ValidatesCurrencyCodes;
use Worksome\Exchange\Contracts\CurrencyCodeProvider;
use Worksome\Exchange\Exceptions\InvalidCurrencyCodeException;

final class ValidateCurrencyCodes implements ValidatesCurrencyCodes
{
    public function __construct(private CurrencyCodeProvider $currencyCodeProvider)
    {
    }

    public function __invoke(array $currencyCodes): array
    {
        $supportedCodes = $this->currencyCodeProvider->all();

        foreach ($currencyCodes as $currencyCode) {
            throw_unless(
                in_array($currencyCode, $supportedCodes),
                new InvalidCurrencyCodeException($currencyCode),
            );
        }

        return $currencyCodes;
    }
}
