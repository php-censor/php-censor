Plugin Atoum
============

Allows you to run [Atoum](https://github.com/atoum/atoum) unit tests.

Configuration
-------------

### Options

- **args** [string, optional] - Allows you to pass command line arguments to Atoum.
- **config** [string, optional] - Path to an Atoum configuration file.
- **directory** [string, optional] - This option lets you specify the tests directory to run.
- **binary_name** [string, optional] - Allows you to provide a name of the binary
- **binary_path** [string, optional] - Allows you to provide a path to the binary
vendor/bin, or a system-provided Atom binary).

### Examples
```yml
  test:
    atoum:
      args: "command line arguments go here"
      config: "path to config file"
      directory: "directory to run tests"
      executable: "path to atoum executable"
```
