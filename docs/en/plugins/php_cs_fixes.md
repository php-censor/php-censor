Plugin PHP Coding Standards Fixer
=================================

Runs PHP Coding Standards Fixer against your build.

Configuration
-------------

### Options

* **verbose** [bool, optional] - Whether to run in verbose mode (default: false)
* **diff** [bool, optional] - Whether to run with the `--diff` flag enabled (default: false)
* **directory** [string, optional] - The directory in which PHP CS Fixer should work (default: `%BUILD_PATH%`)
* **rules** [string, optional] - Fixer rules (default: `@PSR2`)
* **args** [string, optional] - Command line args (in string format) to pass to PHP Coding Standards Fixer (default: ``)

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
