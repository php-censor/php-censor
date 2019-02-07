Plugin Clean build
==================

Works through a list of files to remove from your build. Useful when used in combination with Copy Build or Package 
Build.

Configuration
-------------

### Options

* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.
* **remove** - Required - An array of files and / or directories to remove.

### Examples

```yml
complete:
    clean_build:
        remove:
            - composer.json
            - composer.phar
            - config.dev.php
```
