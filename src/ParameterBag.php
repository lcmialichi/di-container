<?php

namespace DiContainer;

class ParameterBag
{

    public function __construct(private array $parameters = [])
    {
    }

    /**
     * @param string $parameterName
     */
    public function has(string $parameterName): bool
    {
        return isset($this->parameters[$parameterName]);
    }

    /**
     * @param string $parameterName
     */
    public function get(string $parameterName): mixed
    {
        return $this->parameters[$parameterName];
    }
}
