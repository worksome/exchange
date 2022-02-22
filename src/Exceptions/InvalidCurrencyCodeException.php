<?php

declare(strict_types=1);

namespace Worksome\Exchange\Exceptions;

use InvalidArgumentException;

final class InvalidCurrencyCodeException extends InvalidArgumentException
{
    public function __construct(string $invalidCode)
    {
        parent::__construct("[{$invalidCode}] is not a valid currency code.");
    }
}
