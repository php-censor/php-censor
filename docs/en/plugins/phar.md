Plugin Phar
===========

Allows you to create a [Phar](http://php.net/manual/en/book.phar.php) archive from your project.

Configuration
-------------

### Options

* **filename** [string, required] - `phar` filename inside output directory. Default: `build.phar`.
* **directory** [string, optional] - `phar` output directory. Default: `%buildpath%`;
* **regexp**: [string, optional] - regular expression for Phar iterator. Default: `/\.php$/`.
* **stub**: [string, optional] - stub content filename. No default value.

### Examples

```yml
test:
    phar_step:
        plugin:    phar
        directory: /path/to/directory
        filename:  foobar.phar
        regexp:    /\.(php|phtml)$/
        stub:      filestub.php
```
