Plugin PHP Coding Standards Fixer
=================================

Runs PHP Coding Standards Fixer against your build.

Configuration
-------------

### Options

* **directory** [string, optional] - The directory in which PHP CS Fixer should work (default: `%BUILD_PATH%`)
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
    args:      "--rules=@PSR2"
```
