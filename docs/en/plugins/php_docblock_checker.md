Plugin PHP Docblock Checker
===========================

Runs the PHP Docblock Checker against your build. This tool verifies that all classes and methods have docblocks.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **path** - **[DEPRECATED]** Option `path` is deprecated and will be deleted in version 2.0. Use the option 
`directory` instead.
* **skip_methods** - Optional - Tells the checker to ignore methods that don't have a docblock.
* **skip_classes** - Optional - Tells the checker to ignore classes that don't have a docblock.
* **skip_signatures** - Optional - Tells the checker to ignore check docblocks against method signatures.
* **allowed_warnings** [int, optional] - Allow `n` warnings in a successful build (default: 0). 
  Use -1 to allow unlimited warnings.

### Examples

```yml
php_docblock_checker:
    allowed_warnings: 10
    skip_classes: true
```
