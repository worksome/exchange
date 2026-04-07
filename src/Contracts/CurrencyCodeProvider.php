<?php

declare(strict_types=1);

namespace Worksome\Exchange\Contracts;

interface CurrencyCodeProvider
{
    /** @return non-empty-list<string> */
    public function all(): array;
}
