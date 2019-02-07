Plugin Pdepend
==============

Runs [PDepend](http://pdepend.org/) software metrics.

Configuration
-------------

### Options

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **directory** [string, optional] - Directory in which to run PDepend (default: `%BUILD_PATH%`).
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary.


### Examples

```yaml
pdepend:
    directory: ./src
    executable: ./src/vendor/bin/pdepend
```
