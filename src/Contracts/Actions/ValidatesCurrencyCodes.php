<?php

declare(strict_types=1);

namespace Worksome\Exchange\Contracts\Actions;

use Worksome\Exchange\Exceptions\InvalidCurrencyCodeException;

interface ValidatesCurrencyCodes
{
    /**
     * @param array<int, string> $currencyCodes
     * @return array<int, string>
     * @throws InvalidCurrencyCodeException
     */
    public function __invoke(array $currencyCodes): array;
}
