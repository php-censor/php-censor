Plugin Behat
------------

A very simple plugin for running [Behat](http://behat.org/) BDD tests.

Configuration
=============

### Options

- **executable** [string, optional] - Allows you to provide a path to the Behat binary (defaults to PHP Censor root, vendor/bin, or a system-provided Behat binary).
- **features** [string, optional] - Provide a list of Behat features to run.

### Examples
```yml
  test:
    behat:
      executable: "path to behat binary"
      features: "command line arguments"
```
