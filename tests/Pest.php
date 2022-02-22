<?php

use Worksome\Exchange\Support\FlatCurrencyCodeProvider;
use Worksome\Exchange\Tests\TestCase;

uses(TestCase::class)->in('Feature');

/**
 * @return non-empty-array<int, string>
 */
function currencies(): array
{
    return (new FlatCurrencyCodeProvider())->all();
}
