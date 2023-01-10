<?php

namespace Mialichi;

class ParameterBag
{

    public function __construct(private array $parameters = [])
    {
    }

    public function has(string $parameterName)
    {
        return isset($this->parameters[$parameterName]);
    }

    public function get(string $parameterName)
    {
        return $this->parameters[$parameterName];
    }
}
