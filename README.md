# Phloem (Core)

This provides an extensible framework for executing programmatic style behaviour using configuration.

## Installation

```
composer require xylemical/phloem-core
```

## Usage

The most basic usage of the core:

```php
<?php

use Xylemical\Expressions\Math\BcMath;
use Phloem\Core\Expression\ExpressionFactory;

$actions = [
    [
        'if' => '$debug',
        'then' => [
           'set' => [
               'message' => 'This is the $debug value.'
           ]
           'scope' => 'global'
        ]
    ]
];

// Create the expression factory using the bcmath extension.
$expressionFactory = new ExpressionFactory(new BcMath());

// Update the context with environment variables.
$context = $expressionFactory->getContext();
$context->setEnvironment($_ENV);

// Set the global variable 'debug' to TRUE.
$context->setVariable('debug', TRUE);

// Create the action factory, and set the default actions.
$actionFactory = (new ActionFactory())->setDefaults();

// Create a logger (requires installing monolog).
$logger = new \Monolog\Monolog();

// Create the action execution.
$act = new Act($expressionFactory, $actionFactory, $logger);

// Perform the actions.
$act->evaluate($actions);
```

### Actions

See the usage of the default [actions](docs/actions.md).

### Tasks

Tasks defined anywhere within the action hierarchy become globally
available, and overwrite any existing actions, including the inbuilt
ones.

See the task definition under [actions](docs/actions.md) for an
example of a task definition.
