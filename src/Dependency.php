<?php

namespace Mialichi;

use ReflectionParameter;

class Dependency
{

    public function __construct(private ReflectionParameter $dependency)
    {
    }

    public function type()
    {
        return $this->dependency->getType();
    }

    public function isNamedType()
    {
        if ($this->type() instanceof \ReflectionNamedType) {
            return true;
        }

        return false;
    }
    
    public function getTypeHint()
    {
        return $this->type()->getName();
    }

    public function getName(){
        return $this->dependency->getName();
    }

    public function hasDefaultValue()
    {
        return $this->dependency->isOptional();
    }

    public function getDefaultValue()
    {
        if ($this->hasDefaultValue()) {
            return $this->dependency->getDefaultValue();
        }
        return false;
    }
}
