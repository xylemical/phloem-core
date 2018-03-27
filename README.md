# Phloem

This provides an extensible framework for executing programmatic style behaviour using configuration.

## Installation

```
composer require xylemical/phloem
```

## Usage

The most basic usage of phloem:

```php
<?php

$actions = [
    'if' => '$debug',
    'then' => [
       'set' => [
           'message' => 'This is the $debug value.'
       ]
       'scope' => 'global'
    ]
];

// Create the action execution.
$phloem = new Phloem();

// Set the global variable 'debug' to TRUE.
$context = new Context();
$context->setVariable('debug', TRUE);

// Perform the actions.
$phloem->evaluate($actions);

print $context->getVariable('message');
```

### Actions

See the usage of the default [actions](docs/actions.md).

### Tasks

Tasks defined anywhere within the action hierarchy become globally
available, and overwrite any existing actions, including the inbuilt
ones.

See the task definition under [actions](docs/actions.md) for an
example of a task definition.
