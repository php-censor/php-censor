Plugin Behat
============

A very simple plugin for running [Behat](http://behat.org/) BDD tests.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **features** [string, optional] - Provide a list of Behat features to run.

### Examples
```yml
  test:
    behat:
      executable: "path to behat binary"
      features: "command line arguments"
```
