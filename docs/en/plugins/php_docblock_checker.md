Plugin PHP Docblock Checker
===========================

Runs the PHP Docblock Checker against your build. This tool verifies that all classes and methods have docblocks.

Configuration
-------------

### Options

* **skip_methods** - Optional - Tells the checker to ignore methods that don't have a docblock.
* **skip_classes** - Optional - Tells the checker to ignore classes that don't have a docblock.
* **skip_signatures** - Optional - Tells the checker to ignore check docblocks against method signatures.
* **allowed_warnings** [int, optional] - Allow `n` warnings in a successful build (default: 0). 
  Use -1 to allow unlimited warnings.

### Examples

```yaml
test:
    php_docblock_checker:
        allowed_warnings: 10
        skip_classes: true
```

### Additional Options

The following general options can also be used: 

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **directory** [string, optional] - This option lets you specify the tests directory to run.
* **ignore** [optional] - A list of files / paths to ignore (default: build_settings > ignore).
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary vendor/bin, or a system-provided.
* **priority_path** [string, optional] - Priority path for locating the plugin binary (Allowable values: 
  `local` (Local current build path) | 
  `global` (Global PHP Censor 'vendor/bin' path) |
  `system` (OS System binaries path, /bin:/usr/bin etc.). 
  Default order: local -> global -> system)
