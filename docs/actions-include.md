



## include

Includes files via the LoaderInterface, which can extend or encapsulate
smaller chunks of common functionality.

When used as includes at the highest level:

```yaml
# main.yml
- include:
    - tasks/**/*.yaml
-  echo-message:
     message: "Show me the message"
```

```
# tasks/echo.yaml
task: echo-message
actions:
    - echo: "Message: $message"
```

Would echo a message "Message: Show me the message".

When used as include for an action:

```
# main.yml
- if: "$value >= 1"
  then:
    include: actions/do.yml
```

```
# actions/do.yml
- echo: "$value >= 1"
```

Would echo a message "3 >= 1" when 'value' has been set to "3".


