Plugin Behat
============

A very simple plugin for running [Behat](http://behat.org/) BDD tests.

Configuration
-------------

### Options

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **binary_name** [string|array, optional] - Allows you to provide a name of the binary.
* **binary_path** [string, optional] - Allows you to provide a path to the binary.
* **features** [string, optional] - Provide a list of Behat features to run.

### Examples
```yml
  test:
    behat:
      executable: "path to behat binary"
      features: "command line arguments"
```
