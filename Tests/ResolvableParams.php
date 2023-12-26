<?php

namespace Tests;

use DateTime;
use stdClass;

class ResolvableParams
{
    public function __construct(private UnresolvableParams $unresolvableParams)
    {
    }

    public function executeTest(DateTime $time, stdClass $stdClass): bool
    {
        return true;
    }
}