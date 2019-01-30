Plugin PHP TAL Lint
========================

PHP TAL (Template Attribute Language)  linter. https://phptal.org/

This plugin expects the composer package [phptal/phptal](https://packagist.org/packages/phptal/phptal) to be installed.

Configuration
-------------

### Options

* **directory** [string, optional] - directory to inspect (default: build root)
* **ignore** [array, optional] - directory to ignore (default: inherits ignores specified in setup)
* **suffixes** [array, optional] - list of file extensions to inspect. Defaults to `zpt`

### Examples

```yml
test:
  php_tal_lint:
    directory: "app"
    ignore:
      - "vendor"
      - "test"
```
