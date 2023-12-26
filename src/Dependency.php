<?php

namespace DiContainer;

use ReflectionParameter;

class Dependency
{
    public function __construct(private ReflectionParameter $dependency)
    {
    }

    public function type(): \ReflectionNamedType|\ReflectionType|null
    {
        return $this->dependency->getType();
    }

    public function isNamedType(): bool
    {
        if ($this->type() instanceof \ReflectionNamedType) {
            return true;
        }

        return false;
    }

    public function getTypeHint(): string
    {
        return $this->type()->getName();
    }

    public function getName(): string
    {
        return $this->dependency->getName();
    }

    public function hasDefaultValue(): bool
    {
        return $this->dependency->isOptional();
    }

    public function getDefaultValue(): mixed
    {
        if ($this->hasDefaultValue()) {
            return $this->dependency->getDefaultValue();
        }
        return false;
    }
}
