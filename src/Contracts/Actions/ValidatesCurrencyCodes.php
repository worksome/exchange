<?php

declare(strict_types=1);

namespace Worksome\Exchange\Contracts\Actions;

use Worksome\Exchange\Exceptions\InvalidCurrencyCodeException;

interface ValidatesCurrencyCodes
{
    /**
     * @param non-empty-list<string> $currencyCodes
     *
     * @return non-empty-list<string>
     *
     * @throws InvalidCurrencyCodeException
     */
    public function __invoke(array $currencyCodes): array;
}
