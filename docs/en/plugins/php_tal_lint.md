Plugin PHP TAL Lint
===================

PHP TAL (Template Attribute Language)  linter. https://phptal.org/

This plugin expects the composer package [phptal/phptal](https://packagist.org/packages/phptal/phptal) to be installed.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **suffixes** [array, optional] - list of file extensions to inspect. Defaults to `zpt`.

### Examples

```yml
test:
  php_tal_lint:
    directory: "app"
    ignore:
      - "vendor"
      - "test"
```
