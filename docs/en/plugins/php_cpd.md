Plugin PHP Copy/Paste Detector
==============================

Runs PHP Copy/Paste Detector against your build.

Configuration
-------------

### Options

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **path** - **[DEPRECATED]** Option `path` is deprecated and will be deleted in version 2.0. Use the option 
`directory` instead.
* **directory** - Optional - directory in which to run PHP Copy/Paste Detector (default: `%BUILD_PATH%`).
* **ignore** - Optional - A list of files / paths to ignore (default: build_settings > ignore).
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary.


### Examples

```yml
test:
  php_cpd:
    directory: "app"
    ignore:
      - "app/my/path"
```
