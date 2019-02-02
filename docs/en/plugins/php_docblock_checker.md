Plugin PHP Docblock Checker
===========================

Runs the PHP Docblock Checker against your build. This tool verifies that all classes and methods have docblocks.

Configuration
-------------

### Options

* **path** - **[DEPRECATED]** Option `path` is deprecated and will be deleted in version 2.0. Use the option 
`directory` instead.
* **directory** - Optional - Directory in which PHP Docblock Checker should run.
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary
* **binary_path** [string, optional] - Allows you to provide a path to the binary
* **skip_methods** - Optional - Tells the checker to ignore methods that don't have a docblock.
* **skip_classes** - Optional - Tells the checker to ignore classes that don't have a docblock.
* **skip_signatures** - Optional - Tells the checker to ignore check docblocks against method signatures.
* **allowed_warnings** - Optional - The warning limit for a successful build (-1 for no limit).

### Examples

```yml
php_docblock_checker:
    allowed_warnings: 10
    skip_classes: true
```
