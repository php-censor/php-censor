Plugin PHP Copy/Paste Detector
==============================

Runs PHP Copy/Paste Detector against your build.

Configuration
-------------

### Options

* **path** - Optional - Path in which to run PHP Copy/Paste Detector (default: build root).
* **ignore** - Optional - A list of files / paths to ignore (default: build_settings > ignore).

### Examples

```yml
test:
  php_cpd:
    path: "app"
    ignore:
      - "app/my/path"
```
