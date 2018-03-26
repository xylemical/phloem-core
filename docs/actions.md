# Actions

The following are default actions that are provided to control flow
of execution using configuration.

The 'echo' action referenced is only an example action, it is not
defined as a default action.

## if

Provides a conditional flow control.

Examples:

```php
<?php
$config = [
    'if' => '$variable == 3',
    'then' => [
        ['echo' => 'Show me the variable']
    ],
    'else' => [
        ['echo' => 'Show me the other variable']
    ],
];
```

```yaml
if: "$variable == 3"
then:
  - echo: "Show me the variable"
else:
  - echo: "Show me the other variable"
```


## loop

The performs a loop before evaluation the condition to continue the loop.

```php
<?php

$config = [
    'loop' => [
        ['set' => ['value' => '$value - 1']]
    ],
    'until' => '$value <= 0',
];
```

```yaml
loop:
    - set:
        value: '$value - 1'
until: "$value <= 0"
```

## set

This sets variable values for the current current. For instance, the following example sets a global variable 'car' to 1, a 'bar' variable
to '2' when executing the 'then' action, and a 'bar' variable to '3'
when executing the 'else' action.

The 'bar' variable is kept when exiting the 'if' action.

```yaml
- set:
    car: 4
- if: "$car OR 1"
  then:
    - set:
        bar: 2
   else:
     - set:
         bar: 3
       scope: global
```

The current defines how many levels the variable becomes available for by using a positive integer, or with 'global' always making it globally available.

## task

This defines a task which will be able to be run as an action else where.

```yaml
- task: echo-message
  actions:
    - echo: "Message: $message"
- echo-message:
     message: "Show me the message"
```

The firstly defines an "echo-message" task, then subsequently runs the
task using the variable 'message' set to "Show me the message".


## while

The performs a loop before evaluation the condition to continue the loop.

```php
<?php

$config = [
    'while' => '$value > 0',
    'do' => [
        ['set' => ['value' => '$value - 1']]
    ],
];
```

```yaml
while: "$value > 0"
do:
    - set:
        value: '$value - 1'
```
