<?php

require_once __DIR__ . "/../vendor/autoload.php";

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
$resolved = $container->make(UnresolvableParams::class); //outputs: UnresolvableParams instance

// Since we have already bound UnresolvableParams in the container, it will automatically resolve the class.
$resolved = $container->make(ResolvableParams::class); //outputs: ResolvableParams instance

// Resolving a method from a class, in case the dependencies are resolvable.
$resolvedMethod = $container->callable(ResolvableParams::class . "@executeTest"); // outputs: true


