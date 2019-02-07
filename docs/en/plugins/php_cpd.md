Plugin PHP Copy/Paste Detector
==============================

Runs PHP Copy/Paste Detector against your build.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **path** - **[DEPRECATED]** Option `path` is deprecated and will be deleted in version 2.0. Use the option 
`directory` instead.

### Examples

```yml
test:
  php_cpd:
    directory: "app"
    ignore:
      - "app/my/path"
```
