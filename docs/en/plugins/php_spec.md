Plugin PHP Spec
===============

Runs [PHP Spec](http://www.phpspec.net/) tests against your build.

Configuration
-------------

### Options

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary.

### Examples

```
test:
    php_spec:
```
