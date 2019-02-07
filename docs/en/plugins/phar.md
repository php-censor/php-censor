Plugin Phar
===========

Allows you to create a [Phar](http://php.net/manual/en/book.phar.php) archive from your project.

Configuration
-------------

See also [Common Plugin Configuration Options](../plugin_common_options.md).

### Options

* **filename**: `phar` filename inside output directory. Default: `build.phar`.
* **regexp**: regular expression for Phar iterator. Default: `/\.php$/`.
* **stub**: stub content filename. No default value.

### Examples

```yaml
phar:
    directory: /path/to/directory
    filename: foobar.phar
    regexp: /\.(php|phtml)$/
    stub: filestub.php
```
