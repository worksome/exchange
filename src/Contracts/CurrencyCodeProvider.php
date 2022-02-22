<?php

declare(strict_types=1);

namespace Worksome\Exchange\Contracts;

interface CurrencyCodeProvider
{
    /**
     * @return non-empty-array<int, string>
     */
    public function all(): array;
}
