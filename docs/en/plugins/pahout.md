Plugin Pahout
=============

Runs [Pahout](https://github.com/wata727/pahout/) against your build.

Configuration
-------------

### Options

* **directory** [string, optional] - This option lets you specify the tests directory to run (default: `./src`).
* **allowed_warnings** [int, optional] - Allow `n` warnings in a successful build (default: -1). 
  Use -1 to allow unlimited warnings.
  
### Examples

```yaml
test:
  pahout: ~
```

### Additional Options

The following general options can also be used: 

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **ignore** [optional] - A list of files / paths to ignore (default: build_settings > ignore).
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary vendor/bin, or a system-provided.
* **priority_path** [string, optional] - Priority path for locating the plugin binary (Allowable values: 
  `local` (Local current build path) | 
  `global` (Global PHP Censor 'vendor/bin' path) |
  `system` (OS System binaries path, /bin:/usr/bin etc.). 
  Default order: local -> global -> system)

Warning
-------

Pahout requires the following environment:

- [php-ast](https://github.com/nikic/php-ast) v0.1.7 or newer
