Plugin PHP Copy/Paste Detector
==============================

Runs PHP Copy/Paste Detector against your build.

Configuration
-------------

### Options

* **path** - Deprecated - use directory
* **directory** - Optional - direcotry in which to run PHP Copy/Paste Detector (default: `%BUILD_PATH%`).
* **ignore** - Optional - A list of files / paths to ignore (default: build_settings > ignore).
* **executable** [string, optional] -  Allows you to provide a path to phpcs executable

### Examples

```yml
test:
  php_cpd:
    directory: "app"
    ignore:
      - "app/my/path"
```
