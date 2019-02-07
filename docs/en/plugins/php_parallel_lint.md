Plugin PHP Parallel Lint
========================

Similar to the [standard PHP Lint plugin](lint.md), except that it uses the 
[PHP Parallel Lint](https://github.com/JakubOnderka/PHP-Parallel-Lint) project to run.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

None, except [Common Plugin Configuration Options](../plugin_common_options.md)

### Examples

```yml
test:
  php_parallel_lint:
    directory: "app"
    ignore:
      - "vendor"
      - "test"
```
