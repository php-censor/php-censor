Plugin Atoum
============

Allows you to run [Atoum](https://github.com/atoum/atoum) unit tests.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **args** [string, optional] - Allows you to pass command line arguments to Atoum.
* **config** [string, optional] - Path to an Atoum configuration file.
Atom binary).

### Examples
```yml
  test:
    atoum:
      args: "command line arguments go here"
      config: "path to config file"
      directory: "directory to run tests"
      executable: "path to atoum executable"
```
