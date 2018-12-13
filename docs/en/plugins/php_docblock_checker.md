Plugin PHP Docblock Checker
===========================

Runs the PHP Docblock Checker against your build. This tool verifies that all classes and methods have docblocks.

Configuration
-------------

### Options

* **path** - Deprecated - use directory
* **directory** - Optional - Directory in which PHP Docblock Checker should run.
* **skip_methods** - Optional - Tells the checker to ignore methods that don't have a docblock.
* **skip_classes** - Optional - Tells the checker to ignore classes that don't have a docblock.
* **skip_signatures** - Optional - Tells the checker to ignore check docblocks against method signatures.
* **allowed_warnings** - Optional - The warning limit for a successful build.
* **executable** [string, optional] -  Allows you to provide a path to phpdoc-checker executable

### Examples

```yml
php_docblock_checker:
    allowed_warnings: 10
    skip_classes: true
```
