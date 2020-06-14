Plugin PHPUnit
==============

Runs PHPUnit tests against your build.

Configuration
-------------

### Options

Has two modes:

#### phpunit.xml Configuration File

It's activated if you have phpunit.xml file in your build path, `tests/` subfolder, or you specify it as a parameter:

* **config** [string, optional] - Path to a PHP Unit XML configuration file.
* **run_from** [string, optional] - When running PHPUnit with an XML config, the command is run from this directory
* **coverage** [bool, optional] - Value for the `--coverage-html` command line flag.
* **required_classes_coverage** [int|float, optional] - Value of the required percentage of classes coverage.
* **required_methods_coverage** [int|float, optional] - Value of the required percentage of methods coverage.
* **required_lines_coverage** [int|float, optional] - Value of the required percentage of lines coverage.

#### Running Tests By Specifying Directory

* **directories** - Optional - The directories (array) to run PHPUnit on.

Both modes accept:
* **args** - Optional - Command line args (in string format) to pass to PHP Unit

### Examples

Specify config file and test directory:
```yaml
test:
    php_unit:
        config:
            - "path/to/phpunit.xml"
        directories:
            - "app/tests/"
        coverage: true
        required_classes_coverage: 60
        required_methods_coverage: 60
        required_lines_coverage: 60
```

Troubleshooting
---------------

If standard logging of PHP Censor is not enough, to get standard output from any command, including PHPUnit, edit 
`CommandExecutor::executeCommand()` to see what exactly is wrong
* Run `composer update` in phpunit plugin directory of PHP Censor to get all of its dependencies
* If phpunit is inside of the project's composer.json, it might interfere with PHP Censor's phpunit installation
* Make sure you have XDebug installed.`The Xdebug extension is not loaded. No code coverage will be generated.`
Otherwise test report parsing in `TapParser` will fail, wanting coverage report as well `Invalid TAP string, number of 
tests does not match specified test count.`
