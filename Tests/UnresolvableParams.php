<?php

namespace Tests;

class UnresolvableParams
{
    public function __construct(private string $param, private string $param2)
    {

    }
}