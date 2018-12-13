Plugin Pdepend
==============

Runs [PDepend](http://pdepend.org/) software metrics.

Configuration
-------------

### Options

* **directory** [string, optional] - Directory in which to run PDepend (default: `%BUILD_PATH%`).
* **executable** [string, optional] -  Allows you to provide a path to pdepend executable

### Examples

```yaml
pdepend:
    directory: ./src
    executable: ./src/vendor/bin/pdepend
```
