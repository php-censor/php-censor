Plugin Phan
===========

Runs [Phan](https://github.com/phan/phan) against your build.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

None, except [Common Plugin Configuration Options](../plugin_common_options.md)

### Examples

```yml
test:
  phan:
    directory: "app"
    allowed_warnings: 10
    ignore:
      - "app/my/path"
```
