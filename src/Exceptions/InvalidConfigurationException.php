<?php

declare(strict_types=1);

namespace Worksome\Exchange\Exceptions;

use Exception;

final class InvalidConfigurationException extends Exception
{
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }
}
