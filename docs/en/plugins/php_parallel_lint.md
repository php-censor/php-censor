Plugin PHP Parallel Lint
------------------------

Similar to the [standard PHP Lint plugin](lint.md), except that it uses the [PHP Parallel Lint](https://github.com/JakubOnderka/PHP-Parallel-Lint) project to run.

Configuration
=============

### Options

* **directory** [string, optional] - directory to inspect (default: build root)
* **ignore** [array, optional] - directory to ignore (default: inherits ignores specified in setup)

### Examples

```yml
test:
  php_parallel_lint:
    directory: "app"
    ignore:
      - "vendor"
      - "test"
```
