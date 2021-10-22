Plugin Lint
===========

This plugin runs PHP's built in Lint (syntax / error check) functionality.

Configuration
-------------

### Options

* **directories** [array, optional] - An array of paths in which you wish to lint files. This overrides  `directory`.
* **recursive** [bool, optional] - Whether or not you want to recursively check sub-directories of the above (defaults 
to true).

### Examples

```yml
test:
    lint_step:
        plugin: lint
        directory: "single path to lint files"
        directories:
            - "directory to lint files"
            - "directory to lint files"
            - "directory to lint files"
        recursive: false
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
