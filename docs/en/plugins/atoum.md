Plugin Atoum
============

Allows you to run [Atoum](https://github.com/atoum/atoum) unit tests.

Configuration
-------------

### Options

* **args** [string, optional] - Allows you to pass command line arguments to Atoum.
* **config** [string, optional] - Path to an Atoum configuration file.
Atom binary).

### Examples
```yml
test:
    atoum_step:
        plugin: atoum
        args: "command line arguments go here"
        config: "path to config file"
        directory: "directory to run tests"
        executable: "path to atoum executable"
```

### Additional Options

The following general options can also be used: 

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **directory** [string, optional] - This option lets you specify the tests directory to run.
* **ignore** [optional] - A list of files / paths to ignore (default: build_settings > ignore).
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary vendor/bin, or a system-provided.
* **priority_path** [string, optional] - Priority path for locating the plugin binary (Allowable values: 
  `local` (Local current build path) | 
  `global` (Global PHP Censor 'vendor/bin' path) |
  `system` (OS System binaries path, /bin:/usr/bin etc.). 
  Default order: local -> global -> system)
