Plugin Codeception
==================

A simple plugin that allows you to run [Codeception](http://codeception.com/) tests.

Configuration
-------------

### Options

* **config** - Required - Can be either a single string pointing to a Codeception configuration file, or an array of configuration file paths. By default this is called `codeception.yml` and will be in the root of your project.

* **args** - Optional - The string of arguments to be passed to the run command.**Important**, due to the assumption made on line 132 regarding the value of `--xml` being the next argument which will not be correct if the user provides arguments using this config param, you must specify `report.xml` before any user input arguments to satisfy the report processing on line 146.

#### Default values

- config
 - `codeception.yml` if it exists in the root of the project
 - `codeception.dist.yml` if it exists in the root of the project
 - null if no option provided and the above two fail, this will cause an Exception to be thrown on execution

- args
 - Empty string

### Examples

```
  codeception:
    config: "codeception.yml"
    args:   "--no-ansi --coverage-html"
```
