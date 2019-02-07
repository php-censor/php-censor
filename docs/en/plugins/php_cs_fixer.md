Plugin PHP Coding Standards Fixer
=================================

Runs PHP Coding Standards Fixer against your build.

Configuration
-------------

### Options

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **verbose** [bool, optional] - Whether to run in verbose mode (default: false).
* **diff** [bool, optional] - Whether to run with the `--diff` flag enabled (default: false).
* **directory** [string, optional] - The directory in which PHP CS Fixer should work (default: `%BUILD_PATH%`).
* **rules** [string, optional] - Fixer rules (default: `@PSR2`).
* **args** [string, optional] - Command line args (in string format) to pass to PHP 
  Coding Standards Fixer (default: ``).
* **config** [string, optional] - Special config file (default: `%BUILD_PATH%./.php_cs` 
  or `%BUILD_PATH%./.php_cs.dist`).
* **errors** [bool, optional] - Not fix files, but get the number of files with problem (default: false).
* **report_errors** [bool, optional] - With **errors**, get the list of files in "Errors" tab (default: false).
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary.


### Examples

```yml
test:
  php_cs_fixer:
    directory: "./my/dir/path" # == "%BUILD_PATH%/my/dir/path"
    args:      "--rules=@PSR2 --diff --verbose"
```

```yml
test:
  php_cs_fixer:
    directory: "%BUILD_PATH%/my/dir/path"
    verbose:   true
    diff:      true
    rules:     "@PSR2"
```

```yml
test:
  php_cs_fixer:
    config: "./my/dir/.php_cs.special"
```
