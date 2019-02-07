Plugin PHPLoc
=============

Runs [PHPLoc](https://github.com/sebastianbergmann/phploc) against your project and records some key metrics.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

None, except [Common Plugin Configuration Options](../plugin_common_options.md)

### Example

Run PHPLoc against the app directory only. This will prevent inclusion of code from 3rd party libraries that are 
included outside of the app directory.

```yml
test:
  php_loc:
    directory: "app"
```
