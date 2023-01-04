<?php

declare(strict_types=1);

namespace Worksome\Exchange\Contracts\Actions;

use Worksome\Exchange\Exceptions\InvalidCurrencyCodeException;

interface ValidatesCurrencyCodes
{
    /**
     * @param non-empty-array<int, string> $currencyCodes
     *
     * @return non-empty-array<int, string>
     *
     * @throws InvalidCurrencyCodeException
     */
    public function __invoke(array $currencyCodes): array;
}
