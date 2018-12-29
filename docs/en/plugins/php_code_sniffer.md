Plugin PHP Code Sniffer
=======================

Runs PHP Code Sniffer against your build.

Configuration
-------------

### Options

* **allowed_warnings** [int, optional] - Allow `n` warnings in a successful build (default: 0). 
  Use -1 to allow unlimited warnings.
* **allowed_errors** [int, optional] - Allow `n` errors in a successful build (default: 0). 
  Use -1 to allow unlimited errors.
* **suffixes** [array, optional] - An array of file extensions to check.
* **standard** [string, optional] - The standard against which your files should be checked (defaults to PSR2).
* **tab_width** [int, optional] - Your chosen tab width.
* **encoding** [string, optional] - The file encoding you wish to check for.
* **severity** [int, optional] - Allows to set the minimum severity level.
* **error_severity** [int, optional] - Allows to set the minimum errors severity level.
* **warning_severity** [int, optional] - Allows to set the minimum warnings severity level.

### Examples

Simple example where PHPCS will run on app directory, but ignore the views folder, and use PSR-1 and PSR-2 rules for 
validation:

```yaml
test:
    php_code_sniffer:
        directory: "app"
        ignore:
            - "app/views"
        standard: "PSR1,PSR2"
```

For use with an existing project:
```yaml
test:
    php_code_sniffer:
        standard:         "./phpcs.xml"
        allowed_errors:   -1 # Even a single error will cause the build to fail. -1 = unlimited
        allowed_warnings: -1
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
