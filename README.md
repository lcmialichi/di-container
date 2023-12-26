# Dependency Injection

This project demonstrates a simple implementation of dependency injection in PHP using a custom Dependency Injection Container (`DiContainer`). The examples include binding classes to callback solutions, creating class instances, and resolving methods with resolvable dependencies.

## Getting Started
---
### Prerequisites

- PHP environment
- Composer installed

### Installation

```bash
composer require lcmialichi/dicontainer
```


## Example Classes:
---


#### `UnresolvableParams` Class

```php
<?php

namespace Tests;

class UnresolvableParams
{
    private $param1;
    private $param2;

    public function __construct($param1, $param2)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
    }

    // Additional methods or functionality can be added here
}

```

#### `ResolvableParams` Class

```php
<?php

namespace Tests;

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
```

## Dependency Injection Usage
---

```php
<?php

require_once __DIR__ . "/vendor/autoload.php";

use DiContainer\Container;
use Tests\ResolvableParams;
use Tests\UnresolvableParams;

// Create a container instance.
$container = new Container;

// Bind a class to a callback solution.
$container->bind(UnresolvableParams::class, function ($container) {
    return new UnresolvableParams("first parameter", "second parameter");
});

// Create an instance of UnresolvedParams from the container.
$resolved = $container->make(UnresolvableParams::class);

// Since we have already bound UnresolvableParams in the container,
// it will automatically resolve the class.
$resolved = $container->make(ResolvableParams::class);

// Resolving a method from a class, in case the dependencies are resolvable.
$resolvedMethod = $container->callable(ResolvableParams::class . "@executeTest");
var_dump($resolvedMethod) // outputs true;

```