Plugin Phan
===========

Runs [Phan](https://github.com/phan/phan) against your build.

Configuration
-------------

### Options

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **directory** [optional, string] - Directory within which you want Phan to run (default: `%BUILD_PATH%`).
* **ignore** [optional] - A list of files / paths to ignore (default: build_settings > ignore).
* **allowed_warnings** [int, optional] - Allow `n` warnings in a successful build (default: 0). 
  Use -1 to allow unlimited warnings.

### Examples

```yml
test:
  phan:
    directory: "app"
    allowed_warnings: 10
    ignore:
      - "app/my/path"
```
