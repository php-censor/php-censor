Plugin Lint
===========

This plugin runs PHP's built in Lint (syntax / error check) functionality.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **directories** [array, optional] - An array of paths in which you wish to lint files. This overrides  `directory`.
* **recursive** [bool, optional] - Whether or not you want to recursively check sub-directories of the above (defaults 
to true).

### Examples

```yml
  test:
    lint:
      directory: "single path to lint files"
      directories:
        - "directory to lint files"
        - "directory to lint files"
        - "directory to lint files"
     recursive: false
```
