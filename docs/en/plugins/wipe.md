Plugin Wipe
===========

The plugin deletes a directory.

Configuration
-------------

### Options

* **directory** [string, required] - The directory path to delete.
* **allow_failures** [bool, optional] - If true, allow the build to succeed even if this plugin fails.

### Example

```yml
  complete:
    wipe:
      directory: "/path/to/directory"
```
