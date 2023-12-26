<?php

namespace DiContainer;

use ReflectionParameter;

class Container
{
    /**
     * @var array
     */
    private array $container = [];

    /**
     * @var self $instance
     */
    private static ?self $instance = null;

    /**
     * Binds a callback to a class.
     */
    public function bind(string $id, callable $concrete): void
    {
        $this->container[$id] = $concrete;
    }

    /**
     * Returns all bindings within the container.
     */
    public function allBinding(): array
    {
        return $this->container;
    }

    /**
     * Resolves class and method (e.g., Class@method).
     * @throws \RuntimeException
     */
    public function callable (string $classMethod, array $parameters = [])
    {
        $items = explode("@", $classMethod);
        if (count($items) != 2) {
            throw new \RuntimeException("Format not supported by the container!");
        }
        $callback = ($this->make($items[0]))->{$items[1]}(...);
        return $this->make($callback, $parameters);
    }

    /**
     * Removes a service associated with a class.
     */
    public function remove($id): bool
    {
        if (isset($this->container[$id])) {
            unset($this->container[$id]);
            return true;
        }
        return false;
    }

    /**
     * Checks if there is a service associated with a class.
     */
    public function has($id): bool
    {
        return isset($this->container[$id]);
    }

    /**
     * Retrieves the service associated with the class.
     */
    public function get($id): \Closure
    {
        return $this->container[$id];
    }

    /**
     * Resolves dependency cascading.
     * @throws \RuntimeException
     */
    public function make(string|callable $id, array $params = []): mixed
    {
        if (is_callable($id)) {
            return $this->resolveCallable($id, $params);
        }

        if (class_exists($id)) {
            return $this->resolveClass($id, $params);
        }

        throw new \RuntimeException(sprintf("'%s' cannot be resolved", $id));
    }

    private function resolveClass(string $id, array $params = []): mixed
    {
        $reflection = new \ReflectionClass($id);
        if ($this->has($id)) {
            return $this->get($id)($this);
        }

        if (!$this->hasParams($id) && $reflection->isInstantiable()) {
            return new $id;
        }

        if (!$reflection->isInstantiable()) {
            throw new \RuntimeException(sprintf("'%s' cannot be instantiated", $id));
        }

        return $this->resolveInstantiable($id, $params);
    }

    /**
     * @param string $id
     * @param array $params
     */
    private function resolveInstantiable(string $id, array $params = []): mixed
    {
        $resolved = $this->resolveDependencyList(
            $this->constructorParameters($id),
            new ParameterBag($params)
        );
        return new $id(...$resolved ?? []);
    }

    /**
     * @param Closure $callback
     * @param array $params
     */
    private function resolveCallable(callable $callable, array $params = []): mixed
    {
        $resolved = $this->resolveDependencyList(
            $this->closureParameters($callable),
            new ParameterBag($params)
        );
        return $callable(...$resolved ?? []);
    }

    /**
     * @param ReflectionParameter[] $dependencies
     * @param ParameterBag $params
     */
    private function resolveDependencyList(array $dependencies, ParameterBag $params): array
    {
        return array_map(
            function (ReflectionParameter $dependency) use ($params) {
                return $this->resolveDependency(new Dependency($dependency), $params);
            },
            $dependencies
        );
    }

    /**
     * @param Dependency $dependency
     * @param ParameterBag $params
     * @throws \RuntimeException
     */
    public function resolveDependency(Dependency $dependency, ParameterBag $params): mixed
    {
        if ($params->has($name = $dependency->getName())) {
            return $params->get($name);
        }
        if (!$dependency->isNamedType()) {
            throw new \RuntimeException(sprintf("'%s' cannot be resolved", $name));
        }
        if ($dependency->hasDefaultValue()) {
            return $dependency->getDefaultValue();
        }
        return $this->make($dependency->getTypeHint());
    }

    /**
     * Checks if the class has parameters in the constructor.
     * 
     * @param object|string $class
     */
    private function hasParams(object|string $class): bool
    {
        $reflector = new \ReflectionClass($class);
        $constructor = $reflector->getConstructor();
        return $constructor !== null && $constructor->getNumberOfParameters() > 0;
    }

    private function closureParameters(callable $callable): array
    {
        return (new \ReflectionFunction($callable))->getParameters();
    }

    /**
     * @param string $entity
     * @return ReflectionParameter[]
     */
    private function constructorParameters(string $entity): array
    {
        $constructor = (new \ReflectionClass($entity))->getConstructor();
        if (!$constructor) {
            return [];
        }

        return $constructor->getParameters();
    }

    public static function getInstance(): static
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }
}
