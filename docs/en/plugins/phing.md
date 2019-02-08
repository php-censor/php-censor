Plugin Phing
============

This plugin allows you to use the Phing build system to build your project.

Configuration
-------------

### Options

* **build_file** - Your phing build.xml file.
* **targets** - Which build targets you want to run.
* **properties** - Any custom properties you wish to pass to phing.
* **property_file** - A file containing properties you wish to pass to phing.

### Examples

```yaml
setup:
	phing:
        build_file: 'build.xml'
        targets:
            - "build:test"
        properties:
            config_file: "php-censor"
```
