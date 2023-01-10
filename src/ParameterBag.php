<?php

namespace Mialichi;

class ParameterBag
{

    public function __construct(private array $parameters = [])
    {
    }

    /**
     * @param string $parameterName
     */
    public function has(string $parameterName)
    {
        return isset($this->parameters[$parameterName]);
    }

    /**
     * @param string $parameterName
     */
    public function get(string $parameterName)
    {
        return $this->parameters[$parameterName];
    }
}
