Plugin Phan
===========

Runs [Phan](https://github.com/phan/phan) against your build.

Configuration
-------------

### Options

* **directory** [optional, string] - Directory within which you want Phan to run (default: `%BUILD_PATH%`).
* **ignore** [optional] - A list of files / paths to ignore (default: build_settings > ignore).
* **allowed_warnings** [optional, int] - The error limit for a successful build (default: 0). -1 disables warnings.

### Examples

```yml
test:
  phan:
    directory: "app"
    allowed_warnings: 10
    ignore:
      - "app/my/path"
```
